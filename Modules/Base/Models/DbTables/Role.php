<?php

namespace Modules\Base\Models\DbTables;

use \Config\CC as C;

/**
 * Role database table class.
 *
 * Manages user roles and role hierarchy. Roles define permission sets
 * that determine what users can do within the system.
 *
 * Features:
 * - Project isolation via DatabaseExtendProject
 * - Soft delete support via DatabaseExtend
 * - Translation support for role names
 * - Hierarchical role structure (parent-child)
 * - Internal vs external roles
 * - Role data storage (JSON)
 * - Role tree building
 * - Superadmin and user role constants
 *
 * @package   Modules\Base\Models\DbTables
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method static \Modules\Base\Models\Role|array|false getRow($ids = null, $page = false, $order = false, $model = true, $cache = true)
 * @method static \Modules\Base\Models\Role[]|array|false getAll($page = false, $order = false, $model = true)
 */
class Role extends \Application\Assistance\DatabaseExtendProject
{
    /** Primary key field */
    const ROLE_ID = 'role_id';

    /** Role name */
    const ROLE_NAME = 'role_name';

    /** Parent role ID (for hierarchy) */
    const PARENT = 'parent';

    /** Additional role data (JSON) */
    const ROLE_DATA = 'role_data';

    /** Internal role flag */
    const ROLE_INTERNAL = 'role_internal';

    /** Table name */
    public static $_table = 'roles';

    /** Primary key field */
    public static $_index = self::ROLE_ID;

    /** Name field for getByName() lookups */
    public static $_name = self::ROLE_NAME;

    /**
     * Field definitions.
     *
     * @var array
     */
    public static $_fields = [
        self::ROLE_ID => [self::FP_INDEX => true],
        self::ROLE_NAME => [self::FP_TYPE => self::TYPE_STRING, self::FP_LENGTH => 64],
        self::PARENT => [self::FP_INDEX => true, self::FP_NULL => true, self::FP_LINK => Role::class],
        self::ROLE_DATA => [self::FP_TYPE => self::TYPE_STRING],
        self::ROLE_INTERNAL => [self::FP_NULL => true],
    ];

    /**
     * List template for roles.
     *
     * @var array
     */
    protected static $_listTemplate = [
        self::LIST_TEMPLATE_BASE => ['id' => self::ROLE_ID, 'value' => self::ROLE_NAME]
    ];

    // ============================================================================
    // ROLE DATA FIELD KEYS
    // ============================================================================

    /** Task events data key */
    const DATA_TASK_EVENTS = 'events';

    /** Schedule data key */
    const DATA_SHEDULE = 'shedule';

    /** Schedule start time */
    const DATA_SHEDULE_START = 'start';

    /** Schedule next time */
    const DATA_SHEDULE_NEXT = 'next';

    /** Schedule start day/night */
    const DATA_SHEDULE_START_DAY_NIGHT = 'start_day_night';

    /** Schedule hours */
    const DATA_SHEDULE_HOURS = 'hours';

    /** Schedule days count */
    const DATA_SHEDULE_DAYS_COUNT = 'days';

    /** Schedule night count */
    const DATA_SHEDULE_NIGHT_COUNT = 'night';

    /** Schedule day off count */
    const DATA_SHEDULE_DAY_OFF_COUNT = 'day_off';

    /** Internal role flag value */
    const IS_INTERNAL = 1;

    /** Superadmin role ID */
    const ROLE_SUPERADMIN = 1;

    /** Regular user role ID */
    const ROLE_USER = 2;

    /**
     * Get internal roles for a project.
     *
     * @param int $projectId Project ID
     *
     * @return array List of internal role IDs
     */
    public static function getInternalRoles($projectId = CURRENT_PROJECT)
    {
        switch ($projectId) {
            case C::get('cli_project_id'):
                return [self::ROLE_SUPERADMIN];
                break;
            default:
                return [];
                break;
        }
    }

    /** @var array Role map for tree building */
    protected static $map = [];

    /** @var array Role tree structure */
    protected static $tree = [];

    /**
     * Get child roles for a given role.
     *
     * Builds a hierarchical tree of roles.
     *
     * @param bool $roleId Role ID to get children for
     *
     * @return array Role tree or children list
     */
    public static function getChilds($roleId = false)
    {
        static::getAll();
        self::setModelResponse(false);
        $res = static::getByCondition(false, [self::PARENT]);

        if (false !== $res) {
            self::recurse($res);
        }

        return false === $roleId ? self::$tree : (isset(self::$map[$roleId]) ? self::$map[$roleId] : []);
    }

    /**
     * Recursively build role tree.
     *
     * @param array $data Role data
     */
    private static function recurse($data)
    {
        $count = dfCount(self::$map);

        while (dfCount(self::$map) < dfCount($data)) {
            if (dfCount(self::$map) != $count) {
                $count = dfCount(self::$map);
            }

            foreach ($data as $v) {
                $v['_list'] = false;

                if ($v[self::PARENT] > 0) {
                    if (isset(self::$map[$v[self::PARENT]])) {
                        if (false === self::$map[$v[self::PARENT]]['_list']) {
                            self::$map[$v[self::PARENT]]['_list'] = [];
                        }
                        self::$map[$v[self::PARENT]]['_list'][$v[self::ROLE_ID]] = $v;
                        self::$map[$v[self::ROLE_ID]] = &self::$map[$v[self::PARENT]]['_list'][$v[self::ROLE_ID]];
                    }
                } else {
                    self::$map[$v[self::ROLE_ID]] = $v;
                }
            }
        }
    }

    /**
     * Get internal roles only.
     *
     * @return \Modules\Base\Models\Role[]|false
     */
    public static function getInternal()
    {
        return (static::getSelect())
            ->addWhere(static::createWhere(self::ROLE_INTERNAL, self::IS_INTERNAL))
            ->result();
    }
}