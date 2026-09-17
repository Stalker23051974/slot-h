<?php

namespace Modules\Base\Models;

/**
 * RoleRole model class.
 *
 * Represents the many-to-many hierarchical relationship between roles.
 * Defines which roles are parents of other roles.
 *
 * @package   Modules\Base\Models
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method int getRoleRoleId()
 * @method $this setRoleRoleId(int $role_role_id)
 * @method int getParent()
 * @method $this setParent(int $parent)
 * @method int getRoleId()
 * @method $this setRoleId(int $role_id)
 *
 * @method Role|false linkParent()
 * @method Role|false linkRoleId()
 */
class RoleRole extends \Application\Assistance\Model
{
    /** @var int Primary key */
    public $role_role_id;

    /** @var int Parent role ID */
    public $parent;

    /** @var int Child role ID */
    public $role_id;
}