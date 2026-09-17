<?php

namespace Modules\Base\Models\DbTables;

use Application\Assistance\Select as selectObj;
use \Config\CC as C;

/**
 * Rule database table class.
 *
 * Manages system permissions/rules. Rules define specific actions
 * that users with appropriate roles can perform.
 *
 * Features:
 * - Rule grouping and subgrouping
 * - Project isolation (rules can be project-specific or global)
 * - Translation support for rule names
 * - Predefined system rules (system, users, billing, localization, content)
 * - Permission inheritance via roles
 *
 * @package   Modules\Base\Models\DbTables
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method static \Modules\Base\Models\Rule|array|false getRow($ids = null, $page = false, $order = false, $model = true, $cache = true)
 * @method static \Modules\Base\Models\Rule[]|array|false getAll($page = false, $order = false, $model = true)
 */
class Rule extends \Application\Assistance\DatabaseNormal
{
    /** Primary key field */
    const RULE_ID = 'rule_id';

    /** Rule name */
    const RULE_NAME = 'rule_name';

    /** Rule group */
    const RULE_GROUP = 'rule_group';

    /** Rule subgroup */
    const RULE_SUBGROUP = 'rule_subgroup';

    /** Project ID (null for global rules) */
    const PROJECT_ID = 'project_id';

    /** Table name */
    public static $_table = 'rules';

    /** Primary key field */
    public static $_index = self::RULE_ID;

    /** Name field for getByName() lookups */
    public static $_name = self::RULE_NAME;

    /**
     * Field definitions.
     *
     * @var array
     */
    public static $_fields = [
        self::RULE_ID => [self::FP_INDEX => true],
        self::RULE_NAME => [self::FP_TYPE => self::TYPE_STRING, self::FP_LENGTH => 64],
        self::RULE_GROUP => [self::FP_INDEX => true],
        self::RULE_SUBGROUP => [self::FP_INDEX => true, self::FP_NULL => true],
        self::PROJECT_ID => [self::FP_NULL => true, self::FP_LINK => Project::class],
    ];

    /**
     * List template for rules.
     *
     * @var array
     */
    protected static $_listTemplate = [
        self::LIST_TEMPLATE_BASE => [
            'id' => self::RULE_ID,
            'value' => self::RULE_NAME
        ]
    ];

    // rules:
    // 1 2 3 4 5 6 7 8 9 10 11 12 13 14 15      17 18       20 21

    // ============================================================================
    // RULE GROUP CONSTANTS
    // ============================================================================

    /** System management group */
    const GROUP_SYSTEM = 1;

    /** User management group */
    const GROUP_USERS = 2;

    /** Billing/Financial group */
    const GROUP_BILLING = 3;

    /** Localization/Translation group */
    const GROUP_LOCALIZATION = 4;

    /** Content management group */
    const GROUP_CONTENT = 5;

    /** General/total group */
    const GROUP_TOTAL = 6;

    // ============================================================================
    // SYSTEM GROUP RULES (GROUP 1)
    // ============================================================================

    /** Control cron/queue management */
    const RULE_CONTROL_CRON = 1;

    /** Control system constants */
    const RULE_CONTROL_CONSTANTS = 2;

    /** Control URL aliases */
    const RULE_CONTROL_ALIAS = 3;

    /** Control icons */
    const RULE_CONTROL_ICON = 4;

    // ============================================================================
    // USERS GROUP RULES (GROUP 2)
    // ============================================================================

    /** View user list */
    const RULE_SEE_USER = 5;

    /** Edit user data */
    const RULE_EDIT_USER = 6;

    /** View user activity logs */
    const RULE_SEE_PERSON_LOG = 7;

    /** Edit mail templates */
    const RULE_CAN_EDIT_MAILS = 8;

    /** God mode (impersonate users) */
    const ALLOWED_GOD_MODE = 9;

    /** Invite new users */
    const ALLOWED_INVITE_USERS = 20;

    /** Add new users */
    const ALLOWED_ADD_USERS = 21;

    // ============================================================================
    // BILLING GROUP RULES (GROUP 3)
    // ============================================================================

    /** Access billing requests */
    const ALLOWED_ACCESS_REQUESTS = 18;

    // ============================================================================
    // LOCALIZATION GROUP RULES (GROUP 4)
    // ============================================================================

    /** Edit languages */
    const RULE_EDIT_LANGUAGES = 11;

    /** Localize texts */
    const RULE_LOCALIZATION_TEXTS = 12;

    /** Localize code strings */
    const RULE_LOCALIZATION_CODE = 13;

    /** Localize data */
    const RULE_LOCALIZATION_DATA = 14;

    // ============================================================================
    // CONTENT GROUP RULES (GROUP 5)
    // ============================================================================

    /** Control FAQ */
    const RULE_CONTROL_FAQ = 10;

    // ============================================================================
    // TOTAL/GENERAL GROUP RULES (GROUP 6)
    // ============================================================================

    /** Manage roles */
    const RULE_ROLES = 15;

    /** Allow communication */
    const RULE_ALLOW_COMMUNICATION = 17;

    /**
     * Get localized rule group descriptions.
     *
     * @return array Group ID => localized group name
     */
    public static function getGroups()
    {
        return [
            '' . self::GROUP_SYSTEM => C::locale('Система'),
            '' . self::GROUP_USERS => C::locale('Пользователи'),
            '' . self::GROUP_BILLING => C::locale('Биллинг'),
            '' . self::GROUP_TOTAL => C::locale('Общее'),
            '' . self::GROUP_LOCALIZATION => C::locale('Локализация'),
            '' . self::GROUP_CONTENT => C::locale('Контент'),
        ];
    }

    /**
     * Get ecosystem links (project ID to rule mapping).
     *
     * @return array
     */
    public static function getEcosystemLinks()
    {
        return [
            C::get('cli_project_id') => [],
        ];
    }

    /**
     * Get rule subgroups.
     *
     * @return array
     */
    public static function getSubgroups()
    {
        return [];
    }

    /**
     * Get SELECT query with rule filtering.
     *
     * Shows rules that are either global (project_id IS NULL)
     * or specific to the current project.
     *
     * @param int          $type            Query type
     * @param bool         $ignoreTranslate Whether to skip translation
     * @param string|bool  $class           Override class name
     *
     * @return selectObj Configured Select object
     */
    public static function getSelect($type = selectObj::SELECT, $ignoreTranslate = false, $class = false)
    {
        $select = parent::getSelect($type, $ignoreTranslate, $class);
        $select->addWhere(
            (new selectObj\Block(false))
                ->setWhere([
                    self::createWhere(self::PROJECT_ID, CURRENT_PROJECT),
                    self::createWhere(self::PROJECT_ID, null, selectObj\Where::IS_NULL)
                ])
        );
        return $select;
    }
}