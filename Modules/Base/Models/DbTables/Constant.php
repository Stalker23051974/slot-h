<?php

namespace Modules\Base\Models\DbTables;

use \Config\CC as C;

/**
 * Constant database table class.
 *
 * Manages system and user-defined constants stored in the database.
 * Constants provide dynamic configuration values that can be changed
 * at runtime without modifying code.
 *
 * Features:
 * - Project isolation via DatabaseNormalProject
 * - Translation support for constant descriptions
 * - System constants (read-only) vs editable constants
 * - JSON and boolean data types support
 * - Grouped constants for organization
 *
 * @package   Modules\Base\Models\DbTables
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method static \Modules\Base\Models\Constant|array|false getRow($ids = null, $page = false, $order = false, $model = true, $cache = true)
 * @method static \Modules\Base\Models\Constant[]|array|false getAll($page = false, $order = false, $model = true)
 */
class Constant extends \Application\Assistance\DatabaseNormalProject
{
    /** Primary key field */
    const CONSTANT_ID = 'constant_id';

    /** Constant value */
    const CONSTANT_VALUE = 'constant_value';

    /** Constant name (identifier) */
    const CONSTANT_NAME = 'constant_name';

    /** Constant type (system, editable) */
    const CONSTANT_TYPE = 'constant_type';

    /** Constant description (localized) */
    const CONSTANT_DESCRIPTION = 'constant_description';

    /** Additional data (JSON) */
    const CONSTANT_DATA = 'constant_data';

    /** Constant group */
    const CONSTANT_GROUP = 'constant_group';

    /** Table name */
    public static $_table = 'constants';

    /** Primary key field */
    public static $_index = self::CONSTANT_ID;

    /** Name field for getByName() lookups */
    public static $_name = self::CONSTANT_NAME;

    /**
     * List template for constants.
     *
     * @var array
     */
    protected static $_listTemplate = [
        self::LIST_TEMPLATE_BASE => ['id' => self::CONSTANT_ID, 'value' => self::CONSTANT_NAME]
    ];

    /**
     * Field definitions.
     *
     * @var array
     */
    public static $_fields = [
        self::CONSTANT_ID => [],
        self::CONSTANT_VALUE => [self::FP_TYPE => self::TYPE_STRING, self::FP_NULL => true],
        self::CONSTANT_NAME => [self::FP_TYPE => self::TYPE_STRING],
        self::CONSTANT_TYPE => [],
        self::CONSTANT_DESCRIPTION => [self::FP_TYPE => self::TYPE_STRING],
        self::CONSTANT_DATA => [self::FP_TYPE => self::TYPE_TEXT, self::FP_NULL => true],
        self::CONSTANT_GROUP => [],
    ];

    /**
     * Get constant groups with localized names.
     *
     * @return array Group ID => localized group name
     */
    public static function getGroups()
    {
        return [
            self::GROUP_SYSTEM => C::locale('System settings'),
            self::GROUP_USER => C::locale('User settings')
        ];
    }

    // ============================================================================
    // SYSTEM GROUP CONSTANTS
    // ============================================================================

    /** System settings group */
    const GROUP_SYSTEM = 1;

    /** Flag for storing unencrypted passwords (security) */
    const REMEMBER_UNENCRYPTED_PASSWORDS = 'remember_unencrypted_passwords';

    /** Queue statistics collection period */
    const QUEUE_STATISTIC_PERIOD = 'queue_statistic_period';

    // ============================================================================
    // USER GROUP CONSTANTS
    // ============================================================================

    /** User settings group */
    const GROUP_USER = 2;

    /** Authentication timeout (brute force protection) */
    const AUTH_TIMEOUT = 'auth_timeout';

    /** Online user session lifetime */
    const ONLINE_LIFETIME = 'online_timeout';

    /** Session lifetime in seconds */
    const SESSION_LIFETIME = 'session_lifetime';

    /** Superadmin role ID */
    const S_A_ROLE = 'role_superadmin';

    /** Maximum login attempts allowed */
    const MAX_ATTEMPT_ALLOW = 'max_attempt_allow';

    /** Minimum password length */
    const MIN_PASSWORD_LENGTH = 'min_password_length';

    /** Whether password must contain digits */
    const PASSWORD_DECIMAL = 'password_has_decimal';

    /** Whether password must contain uppercase letters */
    const PASSWORD_UPPERCASE = 'password_has_uppercase';

    /** Whether password must contain lowercase letters */
    const PASSWORD_LOWERCASE = 'password_has_lowercase';

    /** Special characters required in password */
    const PASSWORD_SYMBOL = 'password_has_symbol';

    /** Short session TTL for sensitive operations */
    const SHORT_SESSION_TTL = 'short_session_ttl';

    // ============================================================================
    // CONSTANT TYPE CONSTANTS
    // ============================================================================

    /** System constant (read-only, cannot be edited via UI) */
    const TYPE_SYSTEM = 0;

    /** Editable constant (can be modified via UI) */
    const TYPE_EDITABLE = 1;

    // ============================================================================
    // DATA PARAMETER CONSTANTS
    // ============================================================================

    /** Minimum value parameter for validation */
    const DATA_PARAM_MIN = 'min';

    /** Maximum value parameter for validation */
    const DATA_PARAM_MAX = 'max';

    /** JSON data type flag */
    const DATA_PARAM_TYPE_JSON = 'json';

    /** Boolean data type flag */
    const DATA_PARAM_TYPE_BOOLEAN = 'boolean';

    /**
     * Get a constant by name.
     *
     * @param string $name Constant name
     * @param bool   $pop Whether to return a single record or array
     *
     * @return \Modules\Base\Models\Constant|\Modules\Base\Models\Constant[]|false
     */
    public static function getByName($name, $pop = true)
    {
        return (self::getSelect())
            ->addWhere(self::createWhere(self::CONSTANT_NAME, $name))
            ->pop($pop)
            ->result();
    }
}