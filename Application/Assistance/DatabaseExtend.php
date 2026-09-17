<?php

/**
 * Extended database class with soft delete and timestamp support.
 *
 * This class extends the base Database to add automatic handling of:
 * - updated_at: Automatically updated on every save operation
 * - deleted_at: Set to current timestamp on deletion (soft delete)
 *
 * Soft delete means records are not physically removed from the database.
 * Instead, they are marked as deleted by setting the deleted_at field.
 * Queries automatically filter out soft-deleted records unless explicitly
 * overridden with setIgnoreExtend(true).
 *
 * This is useful for:
 * - Audit trails (keep history of deleted records)
 * - Data recovery (undelete accidentally removed records)
 * - Referential integrity (prevent orphaned references)
 *
 * @package   Application\Assistance
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Assistance;

use \Application\Assistance\Select as selectObj;
use \Application\Assistance\Select\Where as whereObj;

class DatabaseExtend extends Database
{
    /** @var string Updated at timestamp field name */
    const FIELD_UPDATED_AT = 'updated_at';

    /** @var string Deleted at timestamp field name (soft delete) */
    const FIELD_DELETED_AT = 'deleted_at';

    /**
     * Base field definitions for this table type.
     * Extended by child classes.
     *
     * @var array
     */
    public static $_fields = [];

    /**
     * Extended fields added to all tables of this type.
     *
     * @var array
     */
    protected static $_extend_data = [
        DatabaseExtend::FIELD_UPDATED_AT => [
            self::FP_TYPE => self::TYPE_DATETIME,
            self::FP_NULL => true
        ],
        DatabaseExtend::FIELD_DELETED_AT => [
            self::FP_TYPE => self::TYPE_DATETIME,
            self::FP_NULL => true
        ],
    ];

    /**
     * Get field definitions including extended fields.
     *
     * @return array Merged field definitions
     */
    public static function getFields()
    {
        return dfArrayMerge(static::$_fields, static::$_extend_data);
    }

    /**
     * Create a SELECT query with soft delete filtering.
     *
     * Automatically adds a WHERE condition to exclude soft-deleted records
     * unless ignoreExtend is explicitly enabled.
     *
     * @param int      $type            Query type
     * @param bool     $ignoreTranslate Whether to skip translation
     * @param string|bool $class        Override class name
     *
     * @return \Application\Assistance\Select Configured Select object
     */
    public static function getSelect($type = selectObj::SELECT, $ignoreTranslate = false, $class = false)
    {
        parent::getSelect($type, $ignoreTranslate, $class === false ? get_called_class() : $class);

        /**
         * Filter out soft-deleted records unless explicitly ignored.
         *
         * When getIgnoreExtend() returns false (default), deleted_at IS NULL
         * is added to the WHERE clause, excluding deleted records.
         *
         * When getIgnoreExtend() returns true, no filtering is applied
         * (used in admin panels to view/restore deleted records).
         */
        if (false === static::getIgnoreExtend()) {
            static::$_select->_where[] = static::createWhere(
                DatabaseExtend::FIELD_DELETED_AT,
                null,
                whereObj::IS_NULL
            );
        } else {
            static::setIgnoreExtend(false);
        }

        return static::$_select;
    }

    /**
     * Create a WHERE condition with proper table context.
     *
     * @param string     $field    Field name
     * @param mixed      $value    Value to compare
     * @param int|null   $condition Condition type
     * @param string|null $table   Table alias
     * @param string|null $foreign  Foreign key for JOIN
     * @param bool       $leftJoin Whether to use LEFT JOIN
     *
     * @return whereObj
     */
    public static function createWhere($field, $value, $condition = null, $table = null, $foreign = null, $leftJoin = true)
    {
        return new selectObj\Where(
            $field,
            $value,
            is_null($condition) ? whereObj::EQUAL : $condition,
            is_null($table) ? static::$_table : $table,
            $foreign,
            $leftJoin
        );
    }

    /**
     * Save a record with automatic updated_at timestamp.
     *
     * Overrides parent to automatically set the updated_at field
     * to the current timestamp on every save operation.
     *
     * For batch updates (multiple rows), updated_at is set for all rows.
     *
     * @param array $params    Field values to save
     * @param int   $projectId Project ID (passed but not used in this method)
     *
     * @return array|bool Save result
     */
    public static function save($params, $projectId = CURRENT_PROJECT)
    {
        $update = static::getSelect(selectObj::REPLACE);

        /**
         * Automatically set updated_at to current timestamp.
         *
         * For batch updates (when primary key is an array):
         * - Set updated_at for each row in the batch
         *
         * For single row updates:
         * - Set updated_at once
         */
        $update->_fields = dfArrayMerge($params, [
            DatabaseExtend::FIELD_UPDATED_AT => is_array($params[static::$_index])
                ? array_fill_keys(dfArrayKeys($params[static::$_index]), \Application\Helpers\Date::dbDateTime())
                : \Application\Helpers\Date::dbDateTime()
        ]);

        /**
         * Clear WHERE conditions for REPLACE operation.
         */
        $update->_where = [];

        $result = static::select();
        return $result;
    }

    /**
     * Soft delete a record.
     *
     * Instead of physically deleting the record, sets the deleted_at
     * field to the current timestamp. The record remains in the database
     * but is filtered out from normal queries.
     *
     * This operation is irreversible without manual database intervention,
     * but can be viewed in admin panels using ignoreExtend.
     *
     * @param array $params Conditions for finding the record to delete
     * @param \Application\Assistance\Model|bool $obj Existing model object (optional)
     *
     * @return array|bool|void Deletion result
     */
    public static function remove($params, $obj = false)
    {
        /**
         * If no model object is provided, find the record first.
         */
        if (false === $obj) {
            $select = static::getSelect();
            foreach ($params as $k => $v) {
                $select->_where[] = static::createWhere($k, $v);
            }
            $obj = static::getByCondition(false, false, true, false, true);
        }

        /**
         * Clear the static cache for this record.
         */
        static::deleteStaticCache($params);

        /**
         * Update the record with deleted_at timestamp.
         * The REPLACE operation will set deleted_at to current time.
         */
        if (false !== $obj) {
            $update = (static::getSelect(selectObj::REPLACE))
                ->setFields(dfArrayMerge($obj->toArray(), [
                    DatabaseExtend::FIELD_DELETED_AT => \Application\Helpers\Date::dbDateTime()
                ]));

            $update->_where = [];
            static::select();
        }
    }
}