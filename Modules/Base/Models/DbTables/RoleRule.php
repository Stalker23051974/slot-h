<?php

namespace Modules\Base\Models\DbTables;

/**
 * RoleRule database table class.
 *
 * Manages the many-to-many relationship between roles and rules (permissions).
 * This table defines which permissions are assigned to each role.
 *
 * Features:
 * - Role-permission assignments
 * - Multiple rules per role
 * - Bulk rule management
 * - Permission inheritance via role hierarchy
 *
 * @package   Modules\Base\Models\DbTables
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method static \Modules\Base\Models\RoleRule|array|false getRow($ids = null, $page = false, $order = false, $model = true, $cache = true)
 * @method static \Modules\Base\Models\RoleRule[]|array|false getAll($page = false, $order = false, $model = true)
 */
class RoleRule extends \Application\Assistance\DatabaseNormal
{
    /** Primary key field */
    const ROLE_RULE_ID = 'role_rule_id';

    /** Role ID (FK to Role table) */
    const ROLE_ID = 'role_id';

    /** Rule ID (FK to Rule table) */
    const RULE_ID = 'rule_id';

    /** Table name */
    public static $_table = 'role_rules';

    /** Primary key field */
    public static $_index = self::ROLE_RULE_ID;

    /**
     * Field definitions.
     *
     * @var array
     */
    public static $_fields = [
        self::ROLE_RULE_ID => [self::FP_INDEX => true],
        self::ROLE_ID => [self::FP_INDEX => true, self::FP_LINK => Role::class],
        self::RULE_ID => [self::FP_LINK => Rule::class],
    ];

    /**
     * Get rules assigned to a role.
     *
     * @param int $roleId Role ID
     *
     * @return \Modules\Base\Models\RoleRule[]|false
     */
    public static function getByRole($roleId)
    {
        return (static::getSelect())
            ->addWhere(static::createWhere(self::ROLE_ID, $roleId))
            ->result();
    }

    /**
     * Save rules for a role (batch update).
     *
     * Removes all existing rules for the role and adds the new ones.
     *
     * @param int   $roleId Role ID
     * @param array $list   Array of rule IDs to assign
     */
    public static function saveRules($roleId, $list)
    {
        if ($res = self::getByRole($roleId)) {
            self::remove([self::ROLE_RULE_ID => dfArrayKeys($res)]);
        }

        if (!empty($list)) {
            foreach ($list as $v) {
                (new \Modules\Base\Models\RoleRule())
                    ->setRoleId($roleId)
                    ->setRuleId($v)
                    ->save();
            }
        }
    }
}