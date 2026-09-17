<?php

namespace Modules\Base\Models;

/**
 * PersonRule model class.
 *
 * Represents user-specific permission rule assignments. Allows
 * adding or removing permissions beyond the user's role.
 *
 * @package   Modules\Base\Models
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method int getPersonRuleId()
 * @method $this setPersonRuleId(int $person_rule_id)
 * @method int getPersonId()
 * @method $this setPersonId(int $person_id)
 * @method int getRuleId()
 * @method $this setRuleId(int $rule_id)
 * @method int getPersonRuleAction()
 * @method $this setPersonRuleAction(int $person_rule_action)
 *
 * @method Person|false linkPersonId()
 * @method Rule|false linkRuleId()
 */
class PersonRule extends \Application\Assistance\Model
{
    /** @var int Primary key */
    public $person_rule_id;

    /** @var int Person ID */
    public $person_id;

    /** @var int Rule ID */
    public $rule_id;

    /** @var int Rule action (add or drop) */
    public $person_rule_action;
}