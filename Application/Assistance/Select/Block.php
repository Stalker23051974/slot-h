<?php

/**
 * SQL WHERE clause block container.
 *
 * This class represents a logical block of WHERE conditions that can be
 * combined using AND or OR operators. Blocks can be nested to create
 * complex query conditions with proper grouping.
 *
 * Examples of what this enables:
 * - (field1 = value1 AND field2 = value2) OR (field3 = value3)
 * - (id IN (1,2,3) AND status = 'active') OR (deleted_at IS NOT NULL)
 * - Nested blocks up to any depth
 *
 * Blocks are used within the Select query builder to construct
 * SQL WHERE clauses with proper parentheses for logical grouping.
 *
 * @package   Application\Assistance\Select
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Assistance\Select;

class Block
{
    /** OR operator: conditions combined with OR */
    const OR_BLOCK = 0;

    /** AND operator: conditions combined with AND */
    const AND_BLOCK = 1;

    /**
     * Operator mapping for SQL generation.
     *
     * @var array
     */
    public $_types = [
        Block::AND_BLOCK => ' AND ',
        Block::OR_BLOCK  => ' OR '
    ];

    /** @var int Block type (AND or OR) */
    public $_type;

    /** @var string|null Table alias this block belongs to */
    public $_from;

    /** @var array Collection of Where or Block objects */
    public $_where = [];

    /** @var mixed Fields for this block (reserved for future use) */
    public $_fields;

    /**
     * Create a new WHERE block.
     *
     * @param bool $type True for AND block, false for OR block
     */
    public function __construct($type = true)
    {
        $this->_type = true === $type ? Block::AND_BLOCK : Block::OR_BLOCK;
    }

    /**
     * Set the table alias for this block.
     *
     * Used when conditions reference a specific table in JOIN queries.
     *
     * @param string $from Table alias
     *
     * @return $this
     */
    public function setFrom($from)
    {
        $this->_from = $from;
        return $this;
    }

    /**
     * Add WHERE conditions to this block.
     *
     * Accepts:
     * - Single Where or Block object
     * - Array of Where or Block objects
     * - Null values are ignored (useful for conditional filtering)
     *
     * @param Where|Block|array|null $where Condition(s) to add
     *
     * @return $this
     *
     * @throws \Application\Assistance\Exception On invalid condition type
     */
    public function setWhere($where)
    {
        if (is_array($where)) {
            foreach ($where as $v) {
                if (null !== $v) {
                    if ($v instanceof Where || $v instanceof Block) {
                        $this->_where[] = $v;
                    } else {
                        new \Application\Assistance\Exception('Invalid condition instantiation', false);
                    }
                }
            }
        } else {
            if ($where !== null) {
                if ($where instanceof Where || $where instanceof Block) {
                    $this->_where[] = $where;
                } else {
                    new \Application\Assistance\Exception('Invalid condition instantiation', false);
                }
            }
        }
        return $this;
    }

    /**
     * Set fields for this block.
     *
     * Reserved for future use (may be used for SELECT field grouping).
     *
     * @param mixed $fields Field definitions
     *
     * @return $this
     */
    public function setFields($fields)
    {
        $this->_fields = $fields;
        return $this;
    }
}