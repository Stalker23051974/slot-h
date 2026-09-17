<?php

namespace Modules\Base\Models\DbTables;

use \Modules\Base\Models\DbTables as db;
use \Config\CC as C;

/**
 * PersonAlert database table class.
 *
 * Manages user alerts and notifications. Stores alerts for various
 * events and system notifications that users need to be aware of.
 *
 * Features:
 * - Multiple alert types (password changes, rights changes, queue alerts)
 * - Time-based filtering
 * - User-specific alerts
 * - TTL-based cleanup
 * - Push notification support
 *
 * @package   Modules\Base\Models\DbTables
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method static \Modules\Base\Models\PersonAlert|array|false getRow($ids = null, $page = false, $order = false, $model = true, $cache = true)
 * @method static \Modules\Base\Models\PersonAlert[]|array|false getAll($page = false, $order = false, $model = true)
 */
class PersonAlert extends \Application\Assistance\DatabaseNormal
{
    /** Primary key field */
    const PERSON_ALERT_ID = 'person_alert_id';

    /** Person ID (FK to Person table) */
    const PERSON_ID = 'person_id';

    /** Alert type */
    const PERSON_ALERT_TYPE = 'person_alert_type';

    /** Alert timestamp */
    const PERSON_ALERT_TIME = 'person_alert_time';

    /** Alert data (JSON or serialized) */
    const PERSON_ALERT_DATA = 'person_alert_data';

    /** Table name */
    public static $_table = 'person_alerts';

    /** Primary key field */
    public static $_index = self::PERSON_ALERT_ID;

    /**
     * Field definitions.
     *
     * @var array
     */
    public static $_fields = [
        self::PERSON_ALERT_ID => [],
        self::PERSON_ID => [self::FP_INDEX => true, self::FP_LINK => Person::class],
        self::PERSON_ALERT_TYPE => [],
        self::PERSON_ALERT_TIME => [self::FP_INDEX => true],
        self::PERSON_ALERT_DATA => [self::FP_TYPE => self::TYPE_STRING]
    ];

    // ============================================================================
    // ALERT TYPE CONSTANTS
    // ============================================================================

    /** Password change alert */
    const ALERT_TYPE_CHANGE_PASSWORD = 1;

    /** Rights/permissions change alert */
    const ALERT_TYPE_CHANGE_RULES = 8;

    /** Queue server alert */
    const ALERT_TYPE_ALERT_QUEUE = 12;

    // ============================================================================
    // DATA KEY CONSTANTS
    // ============================================================================

    /** Count data key */
    const DATA_COUNT = 'count';

    /** Email data key */
    const DATA_EMAIL = 'email';

    /**
     * Get localized alert type descriptions.
     *
     * @return array Alert type => description
     */
    public static function getAlertTypes()
    {
        return [
            self::ALERT_TYPE_CHANGE_PASSWORD => C::locale('Change password'),
            self::ALERT_TYPE_CHANGE_RULES => C::locale('Change of rights'),
            self::ALERT_TYPE_ALERT_QUEUE => C::locale('Queue Server'),
        ];
    }

    /**
     * Get alert types that support push notifications.
     *
     * @return array Alert type => push flag
     */
    public static function getAlarmPushes()
    {
        return [
            self::ALERT_TYPE_ALERT_QUEUE => true,
        ];
    }

    /** Alert person type (reserved) */
    const ALERT_PERSON = 4;

    /**
     * Get active alerts for a person.
     *
     * Filters alerts within the ONLINE_LIFETIME window.
     * Deletes all alerts for the person after retrieval.
     *
     * @param int $personId Person ID
     *
     * @return \Modules\Base\Models\PersonAlert[]|false
     */
    public static function getByPerson($personId)
    {
        $result = (static::getSelect())
            ->addWhere([
                static::createWhere(self::PERSON_ID, $personId),
                static::createWhere(
                    self::PERSON_ALERT_TIME,
                    CURRENT_TIME - C::constant(db\Constant::ONLINE_LIFETIME),
                    \Application\Assistance\Select\Where::MORE_EQUAL
                )
            ])
            ->result();

        // Clean up all alerts for this person
        (static::getSelect(\Application\Assistance\Select::DELETE))
            ->addWhere(static::createWhere(self::PERSON_ID, $personId))
            ->result();

        return $result;
    }

    /**
     * Get active alerts by type.
     *
     * @param int $type Alert type
     *
     * @return \Modules\Base\Models\PersonAlert[]|false
     */
    public static function getActiveByType($type)
    {
        return static::getSelect()
            ->addWhere(static::createWhere(self::PERSON_ALERT_TYPE, $type))
            ->result();
    }
}