<?php

namespace Application\Assistance\Select;

use Application\Assistance\Select;

/**
 * Field handler for building complex SELECT field expressions.
 *
 * This class provides a fluent interface for defining field operations
 * in SELECT queries, including aggregate functions, string manipulations,
 * conditional expressions, and aliases.
 *
 * The class allows chaining multiple operations on a field, which are then
 * compiled into the appropriate SQL expression by the Select builder.
 *
 * @package   Application\Assistance\Select
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */
class FieldHandler
{
    /** @var string|null Database name */
    public $_base;

    /** @var string|null Table name or alias */
    public $_table;

    /** @var string|null Field name */
    public $_field;

    /** @var string|null Field alias in SELECT */
    public $_alias;

    /** @var int|null Action/operation type (from Select constants) */
    public $_action = null;

    /** @var string|null Search pattern for REPLACE operation */
    public $_search;

    /** @var string|int|null Override value for IFNULL or REPLACE */
    public $_override;

    /** @var array|null List of fields for CONCAT operation */
    public $_list;

    /** @var mixed Additional flag for operation modifiers */
    public $_flag;

    /**
     * Apply COUNT aggregate function.
     *
     * @return $this
     */
    public function setCount()
    {
        $this->_action = Select::FIELD_COUNT;
        return $this;
    }

    /**
     * Apply MIN aggregate function.
     *
     * @return $this
     */
    public function setMin()
    {
        $this->_action = Select::FIELD_MIN;
        return $this;
    }

    /**
     * Apply MAX aggregate function.
     *
     * @return $this
     */
    public function setMax()
    {
        $this->_action = Select::FIELD_MAX;
        return $this;
    }

    /**
     * Apply IFNULL function to replace NULL with a default value.
     *
     * @param string|int $override Value to replace NULL with
     *
     * @return $this
     */
    public function setIfNull($override)
    {
        $this->_action = Select::FIELD_IFNULL;
        $this->_override = $override;
        return $this;
    }

    /**
     * Apply REPLACE function to substitute text within the field.
     *
     * @param string       $search   Text to search for
     * @param string|int   $override Replacement text
     *
     * @return $this
     */
    public function setReplace($search, $override)
    {
        $this->_action = Select::FIELD_REPLACE;
        $this->_search = $search;
        $this->_override = $override;
        return $this;
    }

    /**
     * Apply CONCAT function to join multiple fields or strings.
     *
     * @param array $list List of fields or strings to concatenate
     *
     * @return $this
     */
    public function setConcat($list)
    {
        $this->_action = Select::FIELD_CONCAT;
        $this->_list = $list;
        return $this;
    }

    /**
     * Apply GROUP_CONCAT function to aggregate strings.
     *
     * @return $this
     */
    public function setGroupConcat()
    {
        $this->_action = Select::FIELD_GROUP_CONCAT;
        return $this;
    }

    /**
     * Set an alias for the field in the SELECT clause.
     *
     * @param string $value Alias name
     *
     * @return $this
     */
    public function setAlias($value)
    {
        $this->_alias = $value;
        return $this;
    }

    /**
     * Set the database name.
     *
     * @param string $value Database name
     *
     * @return $this
     */
    public function setBase($value)
    {
        $this->_table = $value;
        return $this;
    }

    /**
     * Set the table name or alias.
     *
     * @param string $value Table name or alias
     *
     * @return $this
     */
    public function setTable($value)
    {
        $this->_table = $value;
        return $this;
    }

    /**
     * Set the field name.
     *
     * @param string $value Field name
     *
     * @return $this
     */
    public function setField($value)
    {
        $this->_field = $value;
        return $this;
    }

    /**
     * Set an additional flag for operation modifiers.
     *
     * @param mixed $value Flag value
     *
     * @return $this
     */
    public function setFlag($value)
    {
        $this->_flag = $value;
        return $this;
    }
}