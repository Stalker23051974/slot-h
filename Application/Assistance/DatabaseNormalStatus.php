<?php

/**
 * Standard database class with status filtering (no soft delete).
 *
 * This class extends DatabaseNormal to add automatic status-based
 * filtering without the soft delete features (updated_at, deleted_at)
 * provided by DatabaseExtend classes.
 *
 * Use this for:
 * - Lookup/reference tables that use status filtering
 * - Tables where status determines visibility but soft delete is not needed
 * - System tables that need active/inactive filtering without audit trails
 * - Tables where status filtering is required but soft delete is not
 *
 * For tables that need both status filtering and soft delete,
 * use DatabaseExtendStatus instead.
 *
 * @package   Application\Assistance
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Assistance;

use \Application\Assistance\Select as selectObj;

class DatabaseNormalStatus extends DatabaseNormal
{
    /**
     * Create a SELECT query with status filtering.
     *
     * Overrides parent to add status-based filtering when $_statusUse is true.
     *
     * The filtering works as follows:
     * - When $_statusUse is true (default for most status-enabled tables):
     *   - Only records with status in $_statusIds are returned
     *   - This typically means "active" or "published" records
     *
     * - When $_statusUse is false:
     *   - No status filtering is applied
     *   - All records are returned regardless of status
     *   - Used in admin panels or for reporting
     *
     * The $_statusField property defines which field contains the status value.
     * The $_statusIds property defines which status values are considered "active".
     *
     * @param int          $type            Query type (SELECT, COUNT, etc.)
     * @param bool         $ignoreTranslate Whether to skip translation JOIN
     * @param string|bool  $class           Override class name for model resolution
     *
     * @return \Application\Assistance\Select Configured Select object
     */
    public static function getSelect($type = selectObj::SELECT, $ignoreTranslate = false, $class = false)
    {
        /**
         * Initialize select with parent filters.
         */
        parent::getSelect($type, $ignoreTranslate, $class === false ? get_called_class() : $class);

        /**
         * Apply status filtering if enabled.
         *
         * When $_statusUse is true, only include records where the status
         * field matches one of the values in $_statusIds.
         *
         * The status field is defined by $_statusField.
         * The allowed status values are defined by $_statusIds (int or array).
         */
        if (true === static::$_statusUse) {
            static::$_select->_where[] = static::createWhere(
                static::$_statusField,
                static::$_statusIds
            );
        }

        return static::$_select;
    }
}