<?php

/**
 * MySQL database engine abstract implementation.
 *
 * This class provides MySQL-specific query building and execution logic.
 * It extends the base DbEngine and implements MySQL-specific features:
 * - SELECT query assembly with JOINs, WHERE conditions, ORDER BY, LIMIT
 * - INSERT/REPLACE query generation
 * - COUNT queries for pagination
 * - Foreign key safety checks
 * - Multi-query execution for migrations
 *
 * The query builder automatically handles:
 * - Table/field quoting
 * - SQL injection prevention via parameterization
 * - Query optimization based on field indexes
 * - Complex WHERE conditions with nested blocks
 *
 * @package   Application\Assistance\DbEngine
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Assistance\DbEngine;

use \Application\Assistance\Select as selectObj;
use \Application\Assistance\Database as db;
use \Application\Assistance\Select\Where;
use \Application\Assistance\Select;
use Application\Helpers\Database;
use Application\Helpers\File;

class MysqlAbstract extends DbEngine
{

    // ============================================================================
    // SQL CONDITION TEMPLATES
    // ============================================================================

    /**
     * SQL templates for each condition type.
     * '?' placeholders are replaced with the actual value.
     *
     * @var array
     */
    public static $_conditions = [
        Where::MORE => '>?',
        Where::LESS => '<?',
        Where::MORE_EQUAL => '>=?',
        Where::LESS_EQUAL => '<=?',
        Where::NOT_EQUAL => '<>?',
        Where::EQUAL => '=?',
        Where::IS_NULL => ' IS NULL',
        Where::NOT_NULL => ' IS NOT NULL',
        Where::LIKE => ' LIKE \'%?%\'',
        Where::LIKE_START => ' LIKE \'%?\'',
        Where::LIKE_END => ' LIKE \'?%\'',
        Where::LIKE_EQUAL => ' LIKE \'?\'',
        Where::NOT_LIKE => ' NOT LIKE \'%?%\'',
        Where::NOT_LIKE_START => ' NOT LIKE \'%?\'',
        Where::NOT_LIKE_END => ' NOT LIKE \'?%\'',
        Where::NOT_LIKE_EQUAL => ' NOT LIKE \'?\'',
        Where::REGEXP => ' REGEXP (?)',
        Where::NOT_REGEXP => ' NOT REGEXP (?)',
        Where::FIND => ' FIND_IN_SET (?)',
        Where::NOT_FIND => ' NOT FIND_IN_SET (?)',
        Where::ARR_MORE => 'AND',
        Where::ARR_LESS => 'AND',
        Where::ARR_MORE_EQUAL => 'AND',
        Where::ARR_LESS_EQUAL => 'AND',
        Where::ARR_LIKE => 'OR',
        Where::ARR_LIKE_START => 'OR',
        Where::ARR_LIKE_END => 'OR',
        Where::ARR_NOT_LIKE => 'AND',
        Where::ARR_NOT_LIKE_START => 'AND',
        Where::ARR_NOT_LIKE_END => 'AND',
        Where::ARR_EQUAL => ' IN (?)',
        Where::ARR_NOT_EQUAL => ' NOT IN (?)',
        Where::BEETWEEN => ' BETWEEN ?1 and ?2'
    ];

    /**
     * Query type to SQL keyword mapping.
     *
     * @var array
     */
    public static $_types = [
        Select::SELECT => 'SELECT',
        Select::REPLACE => 'REPLACE INTO',
        Select::DELETE => 'DELETE',
        Select::COUNT => 'SELECT'
    ];

    /**
     * Field action templates for aggregate functions.
     *
     * @var array
     */
    public static $_fieldActions = [
        Select::FIELD_COUNT => 'COUNT(?)',
        Select::FIELD_MAX => 'MAX(?)',
        Select::FIELD_MIN => 'MIN(?)',
        Select::FIELD_IFNULL => 'IFNULL(?,?)',
        Select::FIELD_REPLACE => 'REPLACE(?,?,?)',
        Select::FIELD_CONCAT => 'CONCAT(?)',
        Select::FIELD_GROUP_CONCAT => 'GROUP_CONCAT(?)'
    ];

    /**
     * Assemble a complete SQL query from a Select object.
     *
     * This is the main query builder method that constructs SQL statements
     * for various operations:
     * - SELECT: Standard SELECT queries with JOINs, WHERE, GROUP BY, ORDER BY
     * - COUNT: Row count queries for pagination
     * - REPLACE: INSERT or UPDATE if primary key conflicts
     * - Other: DELETE, UPDATE operations
     *
     * The method intelligently handles:
     * - Field quoting and alias generation
     * - JOIN conditions with LEFT JOIN support
     * - WHERE condition ordering for optimal performance
     * - Pagination with offset/limit
     * - Group by with having clauses
     *
     * @param \Application\Assistance\Select $select Complete SELECT object
     *
     * @return string Assembled SQL query
     */
    public static function assemble($select)
    {
        $join = [];
        $query = '';

        // ====================================================================
        // WHERE CLAUSE ASSEMBLY
        // ====================================================================

        if (!empty($select->_where)) {
            $where = [];
            static::assembleWhere($select, $where, $join, function ($v) {
                return $v[1];
            });

            if (!empty($where)) {
                /**
                 * Sort WHERE conditions by weight/priority.
                 * Indexed conditions are executed first for better performance.
                 */
                usort($where, function ($v0, $v1) {
                    return $v0[0] > $v1[0] ? 1 : ($v0[0] < $v1[0] ? -1 : 0);
                });

                $function = function ($v) {
                    return $v[1];
                };
                $query .= ' WHERE ' . implode(' AND ', dfArrayMap($function, $where));
            }
        }

        // ====================================================================
        // JOIN CLAUSE ASSEMBLY
        // ====================================================================

        $joinText = '';
        if (!empty($join)) {
            foreach ($join as $v) {
                if (!empty($v[1]) && !empty($v[0] && $v[0] != $select->_from)) {
                    $joinText .= ($v[2] == Select\JoinField::LEFT_JOIN ? ' LEFT' : ($v[2] == Select\JoinField::RIGHT_JOIN ? ' RIGHT' : ($v[2] == Select\JoinField::CROSS_JOIN ? ' CROSS' : ''))) . ' JOIN ' . $v[0] . ' ON ';
                    if (is_array($v[1]) || $v[1] instanceof selectObj\Where) {
                        if (!is_array($v[1])) {
                            $v[1] = [$v[1]];
                        }
                        $where = [];
                        foreach ($v[1] as $v1) {
                            $where[] = static::changeWhere($select, $v1);
                        }
                        /**
                         * Sort WHERE conditions by weight/priority.
                         * Indexed conditions are executed first for better performance.
                         */
                        usort($where, function ($v0, $v1) {
                            return $v0[0] > $v1[0] ? 1 : ($v0[0] < $v1[0] ? -1 : 0);
                        });
                        $function = function ($v) {
                            return $v[1];
                        };
                        $joinText .= implode(' AND ', dfArrayMap($function, $where));
                    } else {
                        $joinText .= $v[1];
                    }
                }
            }
        }

        // ====================================================================
        // QUERY TYPE ROUTING
        // ====================================================================

        switch ($select->_type) {
            case selectObj::SELECT:
                /**
                 * Standard SELECT query.
                 * Builds field list with aliases and translations.
                 */
                $response = 'SELECT ' . (empty($select->_fields) ? $select->_from . '.*' : static::generateFieldList($select));
                if (!empty($select->_translateFields)) {
                    if (!is_array($select->_translateFields)) {
                        $select->_translateFields = [$select->_translateFields];
                    }
                    $response .= ',' .
                        implode(',', array_map(function ($v) use ($select) {
                            return self::normalizeField($select, $v);
                        }, $select->_translateFields));
                }
                $response .= ' FROM ' . self::getTableName($select) . ' AS ' . $select->_from . ' ' . $joinText . $query;
                break;

            case selectObj::COUNT:
                /**
                 * COUNT query for pagination.
                 * Counts total rows matching the conditions.
                 */
                $response = 'SELECT '
                    . (empty($select->_fields) ? 'COUNT(*) AS ' . db::$_countField : static::generateFieldList($select))
                    . ' FROM ' . self::getTableName($select) . $joinText . $query;
                break;

            case selectObj::REPLACE:
                /**
                 * REPLACE query (INSERT or UPDATE on duplicate key).
                 * Handles both single and multiple row insertions.
                 */
                $function = function ($v) {
                    return !is_string($v) && !is_numeric($v) && (is_null($v) || empty($v))
                        ? 'null'
                        : '"' . dfAddslashes($v) . '"';
                };

                $response = 'REPLACE INTO ' . self::getTableName($select)
                    . ' (`' . implode('`,`', dfArrayKeys($select->_fields)) . '`) VALUES ';

                $values = dfArrayValues($select->_fields);

                /**
                 * Handle multiple rows: [[row1], [row2], ...]
                 */
                if (is_array($values[0])) {
                    $package = array_combine(dfArrayKeys($values[0]), []);
                    foreach (dfArrayKeys($select->_fields) as $k => $v) {
                        foreach ($values[$k] as $k0 => $v0) {
                            $package[$k0][] = $v0;
                        }
                    }
                    foreach ($package as &$v) {
                        $v = implode(',', dfArrayMap($function, $v));
                    }
                    $response .= '(' . implode('),(', $package) . ')';
                } else {
                    /**
                     * Handle single row.
                     */
                    $response .= '(' . implode(',', dfArrayMap($function, dfArrayValues($select->_fields))) . ')';
                }
                break;

            default:
                /**
                 * Other operations (DELETE, UPDATE, etc.).
                 */
                $response = self::$_types[$select->_type] . ' FROM ' . self::getTableName($select) . $query;
                break;
        }

        // ====================================================================
        // GROUP BY AND HAVING
        // ====================================================================

        if (dfCount($select->_group) > 0) {
            $response .= ' GROUP BY ' . implode(',', $select->_group)
                . ($select->_having > 0 ? ' HAVING COUNT(' . $select->_havingField . ') = ' . $select->_having : '');
        }

        // ====================================================================
        // ORDER BY
        // ====================================================================

        if (false !== $select->_order) {
            $response .= ' ORDER BY ' . implode(',', array_map(function ($v) use ($select) {
                    /**
                     * Quote field names if they don't already have quoting.
                     * Handles complex expressions like functions or spaces.
                     */
                    if ($v instanceof Select\FieldHandler) {
                        $sort = true == $v->_flag ? ' DESC' : '';
                        $v = self::normalizeField($select, $v).$sort;
                    }
                    return preg_match("/( |\(|\`)/", $v)
                        ? $v
                        : preg_replace("/^([^\s]{1,})/", '`$1`', $v);
                }, $select->_order));
        }

        // ====================================================================
        // LIMIT / PAGINATION
        // ====================================================================

        $rowOnPage = false === $select->_offset ? ROW_ON_PAGE : $select->_offset;
        $response .= false !== $select->_page
            ? ' LIMIT ' . ($select->_page * $rowOnPage) . ',' . $rowOnPage
            : '';

        return $response;
    }

    /**
     * Generate a comma-separated field list for SELECT queries.
     *
     * Handles:
     * - Field aliases (AS keyword)
     * - Table prefixing for ambiguous fields
     * - Aggregate functions (COUNT, SUM, etc.)
     * - Custom field expressions
     *
     * @param \Application\Assistance\Select $select SELECT object
     *
     * @return string Comma-separated field list
     */
    protected static function generateFieldList($select)
    {
        $res = [];

        foreach ($select->_fields as $field) {
            $res[] = is_array($field)
                ? preg_replace(
                    "/\?/",
                    (!preg_match("/^[A-Z]{1}/", $field[1]) ? $select->_from . '.' : '') . $field[1],
                    self::$_fieldActions[$field[0]]
                ) . ((isset($field[2]) && !empty($field[2])) ? ' AS ' . $field[2] : '')
                : (preg_match("/\./", $field) || preg_match("/^[A-Z]{1}/", $field[1]) ? '' : $select->_from . '.') . $field;
        }

        return implode(',', $res);
    }

    protected static function normalizeField($select, $field)
    {
        if ($field instanceof Select\FieldHandler) {
            $res = $field->_field instanceof Select\FieldHandler ? self::normalizeField($select, $field->_field) : $field->_field;
            if (!empty($field->_table)) {
                $res = $field->_table . '.' . $res;
            }
            if (!empty($field->_base)) {
                $res = $field->_base . '.' . $res;
            }
            switch ($field->_action) {
                case Select::FIELD_COUNT:
                    $res = 'COUNT(*)';
                    break;
                case Select::FIELD_MIN:
                    $res = 'MIN(' . $res . ')';
                    break;
                case Select::FIELD_MAX:
                    $res = 'MAX(' . $res . ')';
                    break;
                case Select::FIELD_REPLACE:
                    $res = 'REPLACE(' . ($res instanceof Select\FieldHandler ? self::normalizeField($select, $res) : $res) . ',\'' . $field->_search . '\',\''.$field->_override.'\')';
                    break;
                case Select::FIELD_IFNULL:
                    $res = 'IFNULL(' . $res . ',\'' . $field->_override . '\')';
                    break;
                case Select::FIELD_CONCAT:
                    $list = [];
                    foreach ($field->_list as $v) {
                        if ($v instanceof Select\FieldHandler) {
                            $list[] = self::normalizeField($select, $v);
                        } else if (is_array($v)) {
                            $inList = [];
                            foreach ($v as $v0) {
                                if ($v0 instanceof Select\FieldHandler) {
                                    $inList[] = self::normalizeField($select, $v0);
                                } else {
                                    $inList[] = "'" . $v0 . "'";
                                }
                            }
                            $list[] = implode(',', $inList);
                        } else {
                            $list[] = "'" . $v . "'";
                        }
                    }
                    $res = 'CONCAT(' . implode(',', $list) . ')';
                    break;
                case Select::FIELD_GROUP_CONCAT:
                    if ($res instanceof Select\FieldHandler) {
                        $res = self::normalizeField($select, $res);
                    } elseif (is_array($res)) {
                        $iRes = [];
                        foreach ($res as $v0) {
                            $iRes[] = $v0 instanceof Select\FieldHandler ? self::normalizeField($select, $v0) : $v0;
                        }
                        $res = implode(',', $iRes);
                    }
                    $res = 'GROUP_CONCAT(' . $res . ')';
                    break;
            }
            if (!empty($field->_alias)) {
                $res .= ' AS ' . $field->_alias;
            }
            return $res;
        } else {
            return is_array($field)
                ? preg_replace(
                    "/\?/",
                    (!preg_match("/^[A-Z]{1}/", $field[1]) ? $select->_from . '.' : '') . $field[1],
                    self::$_fieldActions[$field[0]]
                ) . ((isset($field[2]) && !empty($field[2])) ? ' AS ' . $field[2] : '')
                : (preg_match("/\./", $field) || preg_match("/^[A-Z]{1}/", $field[1]) ? '' : $select->_from . '.') . $field;
        }
    }

    /**
     * Recursively assemble WHERE conditions from a Select object.
     *
     * Traverses the condition tree and builds SQL WHERE clause components.
     * Handles:
     * - Nested blocks with AND/OR logic
     * - Individual WHERE conditions
     * - JOIN conditions extracted from WHERE
     * - Weight calculation for query optimization
     *
     * @param \Application\Assistance\Select|\Application\Assistance\Select\Block $select Select or Block object
     * @param array $result Reference to WHERE conditions array
     * @param array $join Reference to JOIN conditions array
     * @param callable $function Mapping function for condition formatting
     */
    public static function assembleWhere($select, &$result, &$join, $function)
    {
        if (false !== $select->_where && !empty($select->_where)) {
            foreach ($select->_where as $condition) {
                if ($condition instanceof selectObj\Block) {
                    /**
                     * Nested block: process recursively and wrap in parentheses.
                     */
                    $blockWhere = [];
                    static::assembleWhere($condition, $blockWhere, $join, $function);
                    $result[] = [
                        10,
                        '(' . implode($condition->_types[$condition->_type], dfArrayMap($function, $blockWhere)) . ')'
                    ];
                } else if ($condition instanceof selectObj\Where) {
                    /**
                     * Individual WHERE condition.
                     * Extract JOIN information if this is a foreign key condition.
                     */
                    $from = is_array($condition->_from) ? '`' . implode('`.`', $condition->_from) . '`' : $condition->_from;
                    $join[$from] = [
                        $from,
                        $condition->_foreign,
                        $condition->_leftJoin
                    ];
                    $result[] = static::changeWhere($select, $condition);
                }
            }
        }
    }

    /**
     * Convert a Where object to a SQL condition string.
     *
     * Handles various condition types:
     * - EQUAL, NOT_EQUAL
     * - LIKE, NOT_LIKE (with wildcards)
     * - ARR_EQUAL, ARR_NOT_EQUAL (IN clauses)
     * - BEETWEEN (BETWEEN)
     * - IS NULL / IS NOT NULL
     * - AND/OR grouping for arrays
     *
     * Also calculates weight/priority for query optimization.
     *
     * @param \Application\Assistance\Select $select Parent SELECT object
     * @param selectObj\Where $condition Where condition object
     *
     * @return array [weight, condition_string]
     */
    protected static function changeWhere($select, $condition)
    {
        /**
         * Determine quoting for the value based on condition type.
         * LIKE conditions don't quote the value (wildcards need to work).
         */
        $quote = dfInArray($condition->_condition, [
            $condition::LIKE,
            $condition::LIKE_START,
            $condition::LIKE_END,
            $condition::LIKE_EQUAL,
            $condition::NOT_LIKE,
            $condition::NOT_LIKE_START,
            $condition::NOT_LIKE_END,
            $condition::NOT_LIKE_EQUAL,
            $condition::ARR_EQUAL,
            $condition::ARR_NOT_EQUAL,
        ]) ? '' : '"';

        $value = $condition->_value;

        /**
         * Normalize array values.
         * If array has one element, convert to scalar.
         */

        if ($value instanceof selectObj\JoinField) {
            $value = $value->_base . '.' . $value->_table . '.' . $value->_field;
            $quote = '';
        } else if (is_array($value)) {
            $value = dfArrayUnique($value);
            if (1 === dfCount($value)) {
                $value = array_pop($value);
            } else {
                $value = implode('.', $value);
            }
        }

        $field = $condition->_field;

        /**
         * Normalize array values.
         * If array has one element, convert to scalar.
         */

        if ($field instanceof selectObj\JoinField) {
            $field = $field->_base . '.' . $field->_table . '.' . $field->_field;
            $quote = '';
        } else if (is_array($field)) {
            $field = dfArrayUnique($field);
            if (1 === dfCount($field)) {
                $field = array_pop($field);
            } else {
                $field = implode('.', $field);
            }
        }

        /**
         * Escape values for SQL safety.
         */
        if (is_array($value)) {
            foreach ($value as &$v) {
                $v = dfAddslashes($v);
            }
        } else {
            $value = dfAddslashes($value);
        }

        /**
         * Qualify field name with table alias if not already qualified.
         */
        if (!preg_match("/\./", $field) && !preg_match("/^[A-Z]{1}/", $field)) {
            $field = $condition->_from . '.' . $field;
        }

        /**
         * Calculate condition weight for query optimization.
         * String comparisons are less efficient than integer comparisons.
         * Indexed fields are more efficient than non-indexed.
         */
        $offset = (dfInArray(
                isset($select->_map[$field][db::FP_TYPE]) ? $select->_map[$field][db::FP_TYPE] : db::TYPE_INT,
                db::getStringTypes()
            ) ? 10 : 0) - (isset($select->_map[$field][db::FP_INDEX]) && true === $select->_map[$field][db::FP_INDEX] ? 9 : 0);

        /**
         * Handle NULL checks.
         */
        if ($condition->_condition === false) {
            $result[] = '(' . $field . ' IS NULL OR ' . $field . ' IS NOT NULL)';
        } else {
            if (!is_array($value)) {
                /**
                 * Single value condition.
                 */
                return [
                    $condition->_weight[$condition->_condition] + $offset,
                    $field . preg_replace(
                        '/\?/',
                        $quote . (empty($quote) ? $value : preg_replace("/[\']{1}/", "''", $value)) . $quote,
                        self::$_conditions[$condition->_condition]
                    )
                ];
            } else if ($condition->_condition == $condition::BEETWEEN) {
                /**
                 * BETWEEN condition (two values).
                 */
                return [
                    $condition->_weight[$condition->_condition] + $offset,
                    $field . preg_replace(
                        ['/\?1/', '/\?2/'],
                        $value,
                        self::$_conditions[$condition->_condition]
                    )
                ];
            }

            /**
             * IN / NOT IN with array of values.
             */
            if ($condition->_condition === $condition::EQUAL || $condition->_condition === $condition::NOT_EQUAL) {
                return [
                    $condition->_weight[$condition->_condition] + $offset,
                    $field . ($condition->_condition === $condition::NOT_EQUAL ? ' NOT ' : '')
                    . ' IN ("0","' . implode('","', $value) . '")'
                ];
            }

            /**
             * Multiple conditions with OR logic.
             */
            $result = [];
            foreach ($value as $v) {
                $result[] = $field . preg_replace(
                        '/\?/',
                        $quote . (empty($quote) ? $v : preg_replace("/[\']{1}/", "''", $v)) . $quote,
                        self::$_conditions[$condition->_condition]
                    );
            }
        }

        return [
            $condition->_weight[$condition->_condition] + $offset,
            '(' . implode(self::$_conditions[($condition->_condition + 1000)], $result) . ')'
        ];
    }

    /**
     * Establish a database connection.
     *
     * This method should be overridden by concrete MySQL implementations
     * (mysqli, PDO, etc.). Returns false by default.
     *
     * @param string $base Database name
     * @param string|bool $host Database host
     * @param int|bool $port Database port
     * @param string|bool $user Database user
     * @param string|bool $password Database password
     *
     * @return bool|\PDO|\mysqli Connection object or false
     */
    public static function connect($base, $host = false, $port = false, $user = false, $password = false)
    {
        return false;
    }

    /**
     * Check if a record can be safely deleted.
     *
     * Checks all foreign key constraints to verify that the record
     * is not referenced by other tables. If any references exist,
     * the deletion should be prevented.
     *
     * @param string $table Table name
     * @param string $index Index/key name to check
     * @param mixed $id Record ID to check
     * @param array $params Foreign key constraints [table => [field1, field2]]
     *
     * @return bool True if safe to delete, false if references exist
     *
     * @throws \PDOException On database error
     */
    public static function canSafelyRemove($table, $index, $id, $params)
    {
        if (empty($params)) {
            return true;
        }

        $counter = 0;
        $tables = [];
        $response = [];

        /**
         * Build join conditions for each referenced table.
         */
        foreach ($params as $tbl => $fld) {
            $tables[] = [
                ++$counter,
                $fld[0] . '.' . $tbl,
                is_array($fld[1])
                    ? 't0.' . $index . '=t' . $counter . '.' . implode(' OR t0.' . $index . '=t' . $counter . '.', $fld[1])
                    : 't0.' . $fld[1] . '=t' . $counter . '.' . $fld[1]
            ];
            $response[] = [
                $counter,
                is_array($fld[1]) ? $fld[1][0] : $fld[1]
            ];
        }

        $db = static::connect(false);

        try {
            /**
             * Build and execute query to count references.
             */
            $function = [
                function ($v) {
                    return 'COUNT(t' . $v[0] . '.' . $v[1] . ')';
                },
                function ($v) {
                    return 'LEFT JOIN ' . $v[1] . ' AS t' . $v[0] . ' ON ' . $v[2];
                }
            ];

            $result = $db->query(
                'SELECT ' . implode('+', dfArrayMap($function[0], $response)) . ' AS ' . db::$_countField
                . ' FROM ' . $table . ' AS t0 '
                . implode(' ', dfArrayMap($function[1], $tables))
                . ' WHERE t0.' . $index . '=' . $id
            );
        } catch (\PDOException $e) {
            throw new \PDOException($e);
        }

        $result = self::resultFetch($result);
        return 0 === $result[0][db::$_countField];
    }

    /**
     * Fetch a row from a query result.
     *
     * This method should be overridden by concrete implementations.
     * Returns false by default.
     *
     * @param mixed $result Query result resource/object
     *
     * @return bool False (to be implemented by child classes)
     */
    public static function resultFetch($result)
    {
        return false;
    }
}