<?php

namespace Modules\Base\Models\DbTables;

/**
 * PersonRole database table class.
 *
 * Manages the many-to-many relationship between users (Person) and roles.
 * This table assigns roles to users, determining their permissions
 * and access levels within the system.
 *
 * Features:
 * - User-role assignments
 * - Multiple roles per user
 * - Role-based permission inheritance
 * - Statistics and reporting (counts by role)
 *
 * @package   Modules\Base\Models\DbTables
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method static \Modules\Base\Models\PersonRole|array|false getRow($ids = null, $page = false, $order = false, $model = true, $cache = true)
 * @method static \Modules\Base\Models\PersonRole[]|array|false getAll($page = false, $order = false, $model = true)
 */
class PersonRole extends \Application\Assistance\DatabaseNormal
{
    /** Primary key field */
    const PERSON_ROLE_ID = 'person_role_id';

    /** Person ID (FK to Person table) */
    const PERSON_ID = 'person_id';

    /** Role ID (FK to Role table) */
    const ROLE_ID = 'role_id';

    /** Table name */
    public static $_table = 'person_roles';

    /** Primary key field */
    public static $_index = self::PERSON_ROLE_ID;

    /**
     * Field definitions.
     *
     * @var array
     */
    public static $_fields = [
        self::PERSON_ROLE_ID => [],
        self::PERSON_ID => [self::FP_INDEX => true, self::FP_LINK => Person::class],
        self::ROLE_ID => [self::FP_INDEX => true, self::FP_LINK => Role::class],
    ];

    /**
     * Get role assignments by person ID.
     *
     * @param int|array $personId Person ID(s)
     *
     * @return \Modules\Base\Models\PersonRole[]|\Modules\Base\Models\PersonRole|false
     */
    public static function getByPersonId($personId)
    {
        return (static::getSelect())
            ->addWhere(static::createWhere(self::PERSON_ID, $personId))
            ->pop(!is_array($personId))
            ->result();
    }

    /**
     * Get role assignments by role ID.
     *
     * @param int|int[] $roleId Role ID(s)
     *
     * @return \Modules\Base\Models\PersonRole[]|false
     */
    public static function getByRoleId($roleId)
    {
        return (static::getSelect())
            ->addWhere(static::createWhere(self::ROLE_ID, $roleId))
            ->result();
    }

    /**
     * Get count of users per role.
     *
     * @return array|bool Role ID => count
     */
    public static function getCountsByRoles()
    {
        self::setModelResponse(false);
        return (self::getSelect(\Application\Assistance\Select::SELECT, true))
            ->setGroups([self::ROLE_ID])
            ->setFields([self::ROLE_ID, [\Application\Assistance\Select::FIELD_COUNT, self::PERSON_ID]])
            ->result();
    }
}