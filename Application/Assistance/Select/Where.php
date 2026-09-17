<?php

/**
 * SQL WHERE condition definition.
 *
 * This class represents a single condition in a SQL WHERE clause.
 * It supports a wide variety of comparison operators, including:
 * - Basic comparisons: =, !=, >, <, >=, <=
 * - NULL checks: IS NULL, IS NOT NULL
 * - Pattern matching: LIKE, NOT LIKE (with various wildcard positions)
 * - Regular expressions: REGEXP, NOT REGEXP
 * - Set membership: IN, NOT IN
 * - String search: FIND_IN_SET, NOT FIND_IN_SET
 * - Range: BETWEEN
 *
 * Each condition type has a weight for query optimization,
 * allowing the query builder to reorder conditions for best performance.
 *
 * The class also supports array-based "OR" grouping for multiple values
 * (e.g., field IN (value1, value2, value3) or field LIKE '%a%' OR field LIKE '%b%').
 *
 * @package   Application\Assistance\Select
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Assistance\Select;

class Where
{
    // ============================================================================
    // SINGLE VALUE CONDITION TYPES
    // ============================================================================

    /** Greater than: > */
    const MORE = 1;

    /** Less than: < */
    const LESS = 2;

    /** Greater than or equal: >= */
    const MORE_EQUAL = 3;

    /** Less than or equal: <= */
    const LESS_EQUAL = 4;

    /** Not equal: <> */
    const NOT_EQUAL = 5;

    /** Equal: = */
    const EQUAL = 6;

    /** Is NULL */
    const IS_NULL = 7;

    /** Is NOT NULL */
    const NOT_NULL = 8;

    /** LIKE with wildcards both sides: '%value%' */
    const LIKE = 9;

    /** LIKE with wildcard at start: '%value' */
    const LIKE_START = 91;

    /** LIKE with wildcard at end: 'value%' */
    const LIKE_END = 92;

    /** Exact LIKE (no wildcards): 'value' */
    const LIKE_EQUAL = 93;

    /** NOT LIKE with wildcards both sides: NOT LIKE '%value%' */
    const NOT_LIKE = 10;

    /** NOT LIKE with wildcard at start: NOT LIKE '%value' */
    const NOT_LIKE_START = 101;

    /** NOT LIKE with wildcard at end: NOT LIKE 'value%' */
    const NOT_LIKE_END = 102;

    /** NOT LIKE exact match: NOT LIKE 'value' */
    const NOT_LIKE_EQUAL = 107;

    /** Regular expression match */
    const REGEXP = 103;

    /** Regular expression NOT match */
    const NOT_REGEXP = 104;

    /** FIND_IN_SET search (comma-separated strings) */
    const FIND = 105;

    /** NOT FIND_IN_SET */
    const NOT_FIND = 106;

    /** BETWEEN range: field BETWEEN value1 AND value2 */
    const BEETWEEN = 107;

    // ============================================================================
    // ARRAY VALUE CONDITION TYPES (OR/AND grouping)
    // ============================================================================

    /** Array: field > value1 AND field > value2 ... */
    const ARR_MORE = 1000;

    /** Array: field < value1 AND field < value2 ... */
    const ARR_LESS = 1001;

    /** Array: field >= value1 AND field >= value2 ... */
    const ARR_MORE_EQUAL = 1002;

    /** Array: field <= value1 AND field <= value2 ... */
    const ARR_LESS_EQUAL = 1003;

    /** Array: field NOT IN (value1, value2, ...) */
    const ARR_NOT_EQUAL = 1004;

    /** Array: field IN (value1, value2, ...) */
    const ARR_EQUAL = 1005;

    /** Array: field LIKE '%value1%' OR field LIKE '%value2%' ... */
    const ARR_LIKE = 1008;

    /** Array: field LIKE '%value1' OR field LIKE '%value2' ... */
    const ARR_LIKE_START = 1081;

    /** Array: field LIKE 'value1%' OR field LIKE 'value2%' ... */
    const ARR_LIKE_END = 1082;

    /** Array: field NOT LIKE '%value1%' AND field NOT LIKE '%value2%' ... */
    const ARR_NOT_LIKE = 1009;

    /** Array: field NOT LIKE '%value1' AND field NOT LIKE '%value2' ... */
    const ARR_NOT_LIKE_START = 1091;

    /** Array: field NOT LIKE 'value1%' AND field NOT LIKE 'value2%' ... */
    const ARR_NOT_LIKE_END = 1092;

    // ============================================================================
    // CONDITION WEIGHTS (for query optimization)
    // ============================================================================

    /**
     * Weight values for each condition type.
     *
     * Lower weight = executed earlier = better performance.
     *
     * Conditions are ordered by:
     * 1. NULL checks (fastest)
     * 2. Equality (=) on indexed columns
     * 3. Comparison operators (>, <, >=, <=)
     * 4. NOT NULL
     * 5. IN/NOT IN
     * 6. LIKE patterns (slowest)
     *
     * @var array
     */
    public $_weight = [
        Where::IS_NULL => 0,
        Where::NOT_NULL => 0,
        Where::EQUAL => 1,
        Where::MORE => 2,
        Where::LESS => 2,
        Where::MORE_EQUAL => 3,
        Where::LESS_EQUAL => 3,
        Where::NOT_EQUAL => 2,
        Where::LIKE => 4,
        Where::LIKE_START => 5,
        Where::LIKE_END => 5,
        Where::LIKE_EQUAL => 5,
        Where::NOT_LIKE => 4,
        Where::NOT_LIKE_START => 5,
        Where::NOT_LIKE_END => 5,
        Where::NOT_LIKE_EQUAL => 5,
        Where::REGEXP => 5,
        Where::NOT_REGEXP => 5,
        Where::ARR_MORE => 8,
        Where::ARR_LESS => 8,
        Where::ARR_MORE_EQUAL => 9,
        Where::ARR_LESS_EQUAL => 9,
        Where::FIND => 9,
        Where::NOT_FIND => 9,
        Where::ARR_LIKE => 9,
        Where::ARR_LIKE_START => 10,
        Where::ARR_LIKE_END => 10,
        Where::ARR_NOT_LIKE => 9,
        Where::ARR_NOT_LIKE_START => 10,
        Where::ARR_NOT_LIKE_END => 10,
        Where::ARR_EQUAL => 3,
        Where::ARR_NOT_EQUAL => 3,
        Where::BEETWEEN => 5,
    ];

    // ============================================================================
    // PROPERTIES
    // ============================================================================

    /** @var string Table alias this condition belongs to */
    public $_from;

    /** @var string Foreign key/field for JOIN conditions */
    public $_foreign;

    /** @var string Field name for this condition */
    public $_field;

    /** @var int Condition type constant */
    public $_condition;

    /** @var mixed Value(s) for the condition (scalar or array) */
    public $_value;

    /** @var bool Whether this condition implies a LEFT JOIN */
    public $_leftJoin;

    /**
     * Create a new WHERE condition.
     *
     * @param string $field     Field name (can be qualified with table alias)
     * @param mixed  $value     Value to compare against (scalar or array)
     * @param int    $condition Condition type constant
     * @param string $table     Table alias this condition belongs to
     * @param string $foreign   Foreign key/field for JOIN conditions
     * @param bool   $leftJoin  Whether to use LEFT JOIN (default: true)
     */
    public function __construct($field, $value, $condition, $table, $foreign, $leftJoin = true)
    {
        $this->_field = $field;
        $this->_value = $value;
        $this->_condition = $condition;
        $this->_from = $table;
        $this->_foreign = $foreign;
        $this->_leftJoin = $field instanceof JoinField ? $field->_type : $leftJoin;
    }
}