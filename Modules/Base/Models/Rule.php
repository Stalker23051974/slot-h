<?php

namespace Modules\Base\Models;

/**
 * Rule model class.
 *
 * Represents a permission rule record. Rules define specific actions
 * that users with appropriate roles can perform.
 *
 * @package   Modules\Base\Models
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method int getRuleId()
 * @method $this setRuleId(int $rule_id)
 * @method string getRuleName()
 * @method $this setRuleName(string $rule_name)
 * @method int getRuleGroup()
 * @method $this setRuleGroup(int $rule_group)
 * @method int getRuleSubgroup()
 * @method $this setRuleSubgroup(int $rule_subgroup)
 * @method int getProjectId()
 * @method $this setProjectId(int $project_id)
 *
 * @method Project|false linkProjectId()
 */
class Rule extends \Application\Assistance\Model
{
    /** @var int Primary key */
    public $rule_id;

    /** @var string Rule name */
    public $rule_name;

    /** @var int Rule group */
    public $rule_group;

    /** @var int Rule subgroup */
    public $rule_subgroup;

    /** @var int Project ID */
    public $project_id;
}