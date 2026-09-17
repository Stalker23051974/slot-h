<?php

namespace Modules\Base\Models;

/**
 * RoleRule model class.
 *
 * Represents the many-to-many relationship between roles and rules
 * (permissions). Defines which permissions are assigned to each role.
 *
 * @package   Modules\Base\Models
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method int getRoleRuleId()
 * @method $this setRoleRuleId(int $role_rule_id)
 * @method int getRoleId()
 * @method $this setRoleId(int $role_id)
 * @method int getRuleId()
 * @method $this setRuleId(int $rule_id)
 *
 * @method Role|false linkRoleId()
 * @method Rule|false linkRuleId()
 */
class RoleRule extends \Application\Assistance\Model
{
    /** @var int Primary key */
    public $role_rule_id;

    /** @var int Role ID */
    public $role_id;

    /** @var int Rule ID */
    public $rule_id;
}