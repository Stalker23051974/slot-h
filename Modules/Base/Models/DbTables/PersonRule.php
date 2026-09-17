<?php

namespace Modules\Base\Models\DbTables;

/**
 * PersonRule database table class.
 *
 * Manages user-specific permission rules. This table stores individual
 * user rule overrides that can add or remove specific permissions
 * beyond what the user's role provides.
 *
 * Features:
 * - User-specific rule assignments
 * - Rule addition (grant) and removal (revoke) actions
 * - Fine-grained permission control
 * - Bulk rule management
 *
 * @package   Modules\Base\Models\DbTables
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method static \Modules\Base\Models\PersonRule|array|false getRow($ids = null, $page = false, $order = false, $model = true, $cache = true)
 * @method static \Modules\Base\Models\PersonRule[]|array|false getAll($page = false, $order = false, $model = true)
 */
class PersonRule extends \Application\Assistance\DatabaseNormal
{
    /** Primary key field */
    const PERSON_RULE_ID = 'person_rule_id';

    /** Person ID (FK to Person table) */
    const PERSON_ID = 'person_id';

    /** Rule ID (FK to Rule table) */
    const RULE_ID = 'rule_id';

    /** Rule action (add or drop permission) */
    const PERSON_RULE_ACTION = 'person_rule_action';

    /** Table name */
    public static $_table = 'person_rules';

    /** Primary key field */
    public static $_index = self::PERSON_RULE_ID;

    /**
     * Field definitions.
     *
     * @var array
     */
    public static $_fields = [
        self::PERSON_RULE_ID => [self::FP_INDEX => true],
        self::PERSON_ID => [self::FP_INDEX => true, self::FP_LINK => Person::class],
        self::RULE_ID => [self::FP_LINK => Rule::class],
        self::PERSON_RULE_ACTION => [self::FP_NULL => true],
    ];

    /** Add/grant permission action */
    const RULE_ACTION_ADD = 1;

    /** Drop/revoke permission action */
    const RULE_ACTION_DROP = 2;

    /**
     * Get rules assigned to a person.
     *
     * @param int|array $personId Person ID(s)
     *
     * @return \Modules\Base\Models\PersonRule[]|false
     */
    public static function getByPerson($personId)
    {
        return (static::getSelect())
            ->addWhere(static::createWhere(self::PERSON_ID, $personId))
            ->result();
    }

    /**
     * Get rules by rule ID.
     *
     * @param int $ruleId Rule ID
     *
     * @return \Modules\Base\Models\PersonRule[]|false
     */
    public static function getByRule($ruleId)
    {
        return (static::getSelect())
            ->addWhere(static::createWhere(self::RULE_ID, $ruleId))
            ->result();
    }

    /**
     * Save rules for a person (batch update).
     *
     * Removes all existing rules for the person and adds the new ones.
     *
     * @param int   $personId Person ID
     * @param array $list     Array of rule IDs to assign
     */
    public static function saveRules($personId, $list)
    {
        if ($res = self::getByPerson($personId)) {
            self::remove([self::PERSON_RULE_ID => dfArrayKeys($res)]);
        }

        if (!empty($list)) {
            foreach ($list as $v) {
                (new \Modules\Base\Models\PersonRule())
                    ->setPersonId($personId)
                    ->setRuleId($v)
                    ->save();
            }
        }
    }
}