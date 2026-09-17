<?php

namespace Modules\Base\Models\DbTables;

/**
 * RoleRole database table class.
 *
 * Manages the many-to-many hierarchical relationships between roles.
 * This table defines which roles are parents of other roles,
 * enabling role inheritance and permission propagation.
 *
 * Features:
 * - Role hierarchy management
 * - Parent-child role relationships
 * - Permission inheritance
 * - Role lookup by parent
 *
 * @package   Modules\Base\Models\DbTables
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method static \Modules\Base\Models\RoleRole|array|false getRow($ids = null, $page = false, $order = false, $model = true, $cache = true)
 * @method static \Modules\Base\Models\RoleRole[]|array|false getAll($page = false, $order = false, $model = true)
 */
class RoleRole extends \Application\Assistance\DatabaseNormal
{
    /** Primary key field */
    const ROLE_ROLE_ID = 'role_role_id';

    /** Parent role ID (FK to Role table) */
    const PARENT = 'parent';

    /** Child role ID (FK to Role table) */
    const ROLE_ID = 'role_id';

    /** Table name */
    public static $_table = 'role_roles';

    /** Primary key field */
    public static $_index = self::ROLE_ROLE_ID;

    /**
     * Field definitions.
     *
     * @var array
     */
    public static $_fields = [
        self::ROLE_ROLE_ID => [self::FP_INDEX => true],
        self::PARENT => [self::FP_INDEX => true, self::FP_LINK => Role::class],
        self::ROLE_ID => [self::FP_LINK => Role::class],
    ];

    /**
     * Get role hierarchy entries by parent role ID.
     *
     * @param int $roleId Parent role ID
     *
     * @return \Modules\Base\Models\RoleRule[]|false
     */
    public static function getByRole($roleId)
    {
        return (static::getSelect())
            ->addWhere(static::createWhere(self::PARENT, $roleId))
            ->result();
    }
}