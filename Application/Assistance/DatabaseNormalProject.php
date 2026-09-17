<?php

/**
 * Standard database class with project isolation (no soft delete).
 *
 * This class extends DatabaseNormal to add automatic project filtering
 * without the soft delete features (updated_at, deleted_at) provided
 * by DatabaseExtend classes.
 *
 * Use this for:
 * - Reference/lookup tables that are project-specific but don't need soft delete
 * - Tables where records should never be deleted (only archived elsewhere)
 * - System tables that belong to a project but don't need audit trails
 * - Tables where project isolation is required but soft delete is not
 *
 * For tables that need both soft delete and project isolation,
 * use DatabaseExtendProject instead.
 *
 * @package   Application\Assistance
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Assistance;

use \Application\Assistance\Select as selectObj;

class DatabaseNormalProject extends DatabaseNormal
{
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
     * Adds project_id field linked to the Project model.
     *
     * @var array
     */
    protected static $_extend_data = [
        self::PROJECT_ID => [
            self::FP_LINK => \Modules\Base\Models\DbTables\Project::class
        ],
    ];

    /**
     * Get field definitions including project_id.
     *
     * @return array Merged field definitions
     */
    public static function getFields()
    {
        return dfArrayMerge(static::$_fields, static::$_extend_data);
    }

    /**
     * Create a SELECT query with project filtering.
     *
     * Overrides parent to add project isolation filtering.
     *
     * When ignoreProject is false (default), only records matching
     * the current project (USE_PROJECT) are returned.
     *
     * When ignoreProject is true, records from all projects are returned
     * (used in admin panels for cross-project data management).
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
         * Apply project isolation filtering.
         *
         * When ignoreProject is false, filter by the current project.
         * When ignoreProject is true, skip filtering (show all projects).
         *
         * The filter is applied using USE_PROJECT which is the active
         * project ID (either CURRENT_PROJECT or a CLI override).
         */
        if (false === static::getIgnoreProject()) {
            static::$_select->_where[] = static::createWhere(
                self::PROJECT_ID,
                USE_PROJECT
            );
        } else {
            static::setIgnoreProject(false);
        }

        return static::$_select;
    }
}