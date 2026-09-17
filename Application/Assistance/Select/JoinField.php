<?php

namespace Application\Assistance\Select;

/**
 * Join field definition for building JOIN conditions.
 *
 * This class represents a field reference used in JOIN conditions.
 * It allows specifying the database, table, and field name separately,
 * which is useful when working with multiple databases or when
 * table aliases are needed in complex queries.
 *
 * The class provides a fluent interface for setting properties and
 * supports different JOIN types: INNER JOIN, LEFT JOIN, RIGHT JOIN, CROSS JOIN.
 *
 * @package   Application\Assistance\Select
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */
class JoinField
{
    /** INNER JOIN type */
    const JOIN = 1;

    /** LEFT JOIN type */
    const LEFT_JOIN = 2;

    /** RIGHT JOIN type */
    const RIGHT_JOIN = 3;

    /** CROSS JOIN type */
    const CROSS_JOIN = 4;

    /** @var string|null Database name */
    public $_base;

    /** @var string|null Table name or alias */
    public $_table;

    /** @var string|null Field name */
    public $_field;

    /** @var int Join type (JOIN, LEFT_JOIN, RIGHT_JOIN, CROSS_JOIN) */
    public $_type;

    /**
     * Constructor.
     *
     * @param string|null $base  Database name
     * @param string|null $table Table name or alias
     * @param string|null $field Field name
     * @param mixed|null  $value Reserved for future use
     */
    public function __construct($base = null, $table = null, $field = null, $value = null)
    {
        $this->_base = $base;
        $this->_table = $table;
        $this->_field = $field;
        $this->_type = self::JOIN;
    }

    /**
     * Set the database name.
     *
     * @param string $base Database name
     *
     * @return $this
     */
    public function setBase($base)
    {
        $this->_base = $base;
        return $this;
    }

    /**
     * Set the table name or alias.
     *
     * @param string $table Table name or alias
     *
     * @return $this
     */
    public function setTable($table)
    {
        $this->_table = $table;
        return $this;
    }

    /**
     * Set the field name.
     *
     * @param string $field Field name
     *
     * @return $this
     */
    public function setField($field)
    {
        $this->_field = $field;
        return $this;
    }

    /**
     * Set LEFT JOIN type.
     *
     * @return $this
     */
    public function leftJoin()
    {
        $this->_type = self::LEFT_JOIN;
        return $this;
    }

    /**
     * Set RIGHT JOIN type.
     *
     * @return $this
     */
    public function rightJoin()
    {
        $this->_type = self::RIGHT_JOIN;
        return $this;
    }

    /**
     * Set CROSS JOIN type.
     *
     * @return $this
     */
    public function crossJoin()
    {
        $this->_type = self::CROSS_JOIN;
        return $this;
    }
}