<?php

/**
 * Extended database class with project isolation.
 *
 * This class extends DatabaseExtend to add automatic project filtering
 * for multi-project systems. Each record is associated with a specific
 * project, and queries automatically filter by the current project.
 *
 * Features:
 * - Automatic project_id filtering on all queries
 * - Inherits soft delete (updated_at, deleted_at) from DatabaseExtend
 * - Project field is linked to the Project model for relationships
 * - Can be disabled with setIgnoreProject(true) for admin purposes
 *
 * This is the most commonly used database class for project-specific data
 * that requires both soft delete and project isolation.
 *
 * @package   Application\Assistance
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Assistance;

use \Application\Assistance\Select as selectObj;
use \Application\Assistance\Select\Where as whereObj;

class DatabaseExtendProject extends DatabaseExtend
{
    /**
     * Extended fields including project_id.
     *
     * Adds project_id to the inherited updated_at and deleted_at fields.
     * The PROJECT_ID field is linked to the Project model for ORM relationships.
     *
     * @var array
     */
    protected static $_extend_data = [
        self::FIELD_UPDATED_AT => [
            self::FP_TYPE => self::TYPE_DATETIME,
            self::FP_NULL => true
        ],
        self::FIELD_DELETED_AT => [
            self::FP_TYPE => self::TYPE_DATETIME,
            self::FP_NULL => true
        ],
        self::PROJECT_ID => [
            self::FP_LINK => \Modules\Base\Models\DbTables\Project::class
        ],
    ];

    /**
     * Create a SELECT query with project filtering.
     *
     * Overrides parent to add:
     * 1. Soft delete filtering (inherited from DatabaseExtend)
     * 2. Project isolation filtering (added in this class)
     *
     * The project filter uses self::$_usedProject, which defaults to
     * CURRENT_PROJECT but can be overridden with sertUsedProject().
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
         * Initialize select with parent filters (including soft delete).
         */
        parent::getSelect($type, $ignoreTranslate, $class === false ? get_called_class() : $class);

        /**
         * Apply soft delete filtering.
         *
         * If ignoreExtend is false (default), only show records where
         * deleted_at IS NULL.
         *
         * If ignoreExtend is true, show all records including deleted ones
         * (used in admin panels for viewing/restoring deleted records).
         */
        if (false === static::getIgnoreExtend()) {
            static::$_select->_where[] = static::createWhere(
                self::FIELD_DELETED_AT,
                null,
                whereObj::IS_NULL
            );
        } else {
            static::setIgnoreExtend(false);
        }

        /**
         * Apply project isolation filtering.
         *
         * If ignoreProject is false (default), only show records where
         * project_id matches the current project.
         *
         * If ignoreProject is true, show records from all projects
         * (used in admin panels for cross-project data management).
         *
         * After applying the filter, reset $_usedProject to the default
         * value to prevent unintended reuse.
         */
        if (false === static::getIgnoreProject()) {
            static::$_select->_where[] = static::createWhere(
                self::PROJECT_ID,
                self::$_usedProject
            );
            self::$_usedProject = CURRENT_PROJECT;
        } else {
            static::setIgnoreProject(false);
        }

        return static::$_select;
    }
}