<?php

/**
 * SQL SELECT query builder.
 *
 * This class provides a fluent interface for building database queries.
 * It is the core component of the ORM's query construction system.
 *
 * Features:
 * - Fluent method chaining for query construction
 * - Support for SELECT, REPLACE, DELETE, and COUNT queries
 * - WHERE conditions with AND/OR grouping
 * - JOIN support through WHERE conditions
 * - ORDER BY with ASC/DESC
 * - GROUP BY with HAVING
 * - Pagination (LIMIT/OFFSET)
 * - Field selection with aliases
 * - Aggregate functions (COUNT, MAX, MIN, SUM)
 * - Query caching control
 * - Automatic table/field quoting
 *
 * Usage example:
 *   $select = new Select(Select::SELECT, 'Person');
 *   $select->addWhere($where)
 *          ->order('name')
 *          ->page(2)
 *          ->result();
 *
 * @package   Application\Assistance
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Assistance;

use Application\Assistance\Select\FieldHandler;
use Config\CC as C;

class Select
{
    // ============================================================================
    // QUERY TYPE CONSTANTS
    // ============================================================================

    /** SELECT query */
    const SELECT = 0;

    /** REPLACE query (INSERT or UPDATE on duplicate key) */
    const REPLACE = 1;

    /** DELETE query */
    const DELETE = 2;

    /** COUNT query (returns row count) */
    const COUNT = 3;

    // ============================================================================
    // FIELD AGGREGATE FUNCTION CONSTANTS
    // ============================================================================

    /** COUNT aggregate function */
    const FIELD_COUNT = 1;

    /** MAX aggregate function */
    const FIELD_MAX = 2;

    /** MIN aggregate function */
    const FIELD_MIN = 3;

    /** IFNULL function */
    const FIELD_IFNULL = 4;

    /** REPLACE function */
    const FIELD_REPLACE = 5;

    /** CONCAT function */
    const FIELD_CONCAT = 6;

    /** GROUP_CONCAT aggregate function */
    const FIELD_GROUP_CONCAT = 7;

    // ============================================================================
    // SORT ORDER CONSTANTS
    // ============================================================================

    /** Ascending sort order */
    const SORT_ASC = 'ASC';

    /** Descending sort order */
    const SORT_DESC = 'DESC';

    // ============================================================================
    // QUERY PROPERTIES
    // ============================================================================

    /** @var int Query type (SELECT, REPLACE, DELETE, COUNT) */
    public $_type;

    /** @var string Table name/alias */
    public $_from;

    /** @var array WHERE conditions (Where or Block objects) */
    public $_where = [];

    /** @var array JOIN (Join objects) */
    public $_join = [];

    /** @var array Selected fields */
    public $_fields = [];

    /** @var string[] Translation fields for localized content */
    public $_translateFields = [];

    /** @var bool|int Page number for pagination */
    public $_page = false;

    /** @var bool|int Row offset/limit */
    public $_offset = false;

    /** @var bool Whether to use cache */
    public $_cache = true;

    /** @var bool Whether to return a single record */
    public $_pop = false;

    /**
     * ORDER BY clause.
     *
     * @var array|bool
     */
    public $_order = false;

    /**
     * GROUP BY clause.
     *
     * @var array|bool
     */
    public $_group = [];

    /** @var bool|int HAVING condition count */
    public $_having = false;

    /** @var string HAVING field */
    public $_havingField = '*';

    /** @var string Database name */
    public $_base = 'base';

    /** @var array Field mapping (for type and index info) */
    public $_map = [];

    /** @var string|bool Primary key field */
    public $_primary = false;

    /** @var \Application\Assistance\Database Database class reference */
    private $_class = '\Application\Assistance\Database';

    /**
     * Create a new SELECT query object.
     *
     * @param int    $type  Query type (SELECT, REPLACE, DELETE, COUNT)
     * @param string $class Database class name for result processing
     */
    public function __construct($type, $class)
    {
        $this->_base = C::get(C::get()->main_database)->db_name;
        $this->_type = $type;
        $this->_class = $class;
    }

    // ============================================================================
    // FLUENT QUERY BUILDING METHODS
    // ============================================================================

    /**
     * Add WHERE conditions to the query.
     *
     * Accepts single Where/Block objects or arrays of them.
     * Null values are ignored (useful for conditional filtering).
     *
     * @param mixed $arr Where condition or array of conditions
     *
     * @return $this
     */
    public function addWhere($arr = [])
    {
        if (is_array($arr) && dfCount($arr) > 0) {
            foreach ($arr as $v) {
                if (null !== $v) {
                    $this->_where[] = $v;
                }
            }
        } else {
            $this->_where[] = $arr;
        }
        return $this;
    }

    /**
     * Add JOIN to the query.
     *
     * Accepts single Join objects or arrays of them.
     * Null values are ignored (useful for conditional filtering).
     *
     * @param mixed $arr Where condition or array of conditions
     *
     * @return $this
     */
    public function addJoin($arr = [])
    {
        if (is_array($arr) && dfCount($arr) > 0) {
            foreach ($arr as $v) {
                if (null !== $v) {
                    $this->_join[] = $v;
                }
            }
        } else {
            $this->_join[] = $arr;
        }
        return $this;
    }

    /**
     * Set the SELECT field list.
     *
     * @param array $arr Array of field names or field definitions
     *
     * @return $this
     */
    public function setFields($arr = [])
    {
        $this->_fields = $arr;
        return $this;
    }

    /**
     * Set GROUP BY fields.
     *
     * @param array $arr Array of field names to group by
     *
     * @return $this
     */
    public function setGroups($arr = [])
    {
        $this->_group = $arr;
        return $this;
    }

    /**
     * Set HAVING condition for grouped queries.
     *
     * @param int    $count Minimum count for HAVING
     * @param string $field Field to count
     *
     * @return $this
     */
    public function setHaving($count, $field = '*')
    {
        $this->_having = $count;
        $this->_havingField = $field;
        return $this;
    }

    /**
     * Set the page number for pagination.
     *
     * @param int $page Page number (0-based)
     *
     * @return $this
     */
    public function page($page)
    {
        $this->_page = $page;
        return $this;
    }

    /**
     * Set ORDER BY clause.
     *
     * @param string|array $order Field(s) to order by
     * @param bool         $type  True for ASC, false for DESC
     *
     * @return $this
     */
    public function order($order = null, $type = true)
    {
        if (is_array($order)) {
            $this->_order = [];
            foreach ($order as $k => $v) {
                if (!($v instanceof FieldHandler)) {
                    $this->_order[] = (preg_match("/( |\(|\`)/", $k) ? $k : '`' . $k . '`')
                        . (true === $v ? '' : ' ' . self::SORT_DESC);
                } else {
                    $this->_order[] = $v;
                }
            }
        } else if (null !== $order) {
            $this->_order = [
                !($order instanceof FieldHandler) ?
                    $order :
                    ((preg_match("/( |\(|\`)/", $order) ?
                            $order :
                            '`' . $order . '`'
                        )
                    . (true === $type ? '' : ' ' . self::SORT_DESC)
                    )];
        }
        return $this;
    }

    /**
     * Enable or disable caching for this query.
     *
     * @param bool $cache Whether to use cache
     *
     * @return $this
     */
    public function cache($cache)
    {
        $this->_cache = $cache;
        return $this;
    }

    /**
     * Set the row limit/offset.
     *
     * @param int $offset Number of rows to return
     *
     * @return $this
     */
    public function offset($offset)
    {
        $this->_offset = $offset;
        return $this;
    }

    /**
     * Set whether to return a single record.
     *
     * When true, returns the first record instead of an array.
     *
     * @param bool $pop Whether to pop the first record
     *
     * @return $this
     */
    public function pop($pop = true)
    {
        $this->_pop = $pop;
        return $this;
    }

    /**
     * Execute the query and return results.
     *
     * Delegates to the database class's getByCondition method.
     *
     * @return mixed Query results (array, model, or count)
     */
    public function result()
    {
        return ($this->_class)::getByCondition(
            $this->_page,
            $this->_order,
            $this->_cache,
            $this->_offset,
            $this->_pop
        );
    }
}