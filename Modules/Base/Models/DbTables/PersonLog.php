<?php

namespace Modules\Base\Models\DbTables;

use Application\Assistance\Select\FieldHandler;
use Application\Assistance\Select\Where;
use \Config\CC as C;

/**
 * PersonLog database table class.
 *
 * Manages user activity logging and audit trails. Records all user actions
 * including page views, data modifications, and system operations.
 *
 * Features:
 * - Comprehensive user action logging
 * - IP address tracking
 * - Module/controller/action context
 * - Entity tracking (which record was affected)
 * - Change tracking (before/after values)
 * - Archive support for old logs
 * - Analytics queries (charts, statistics)
 *
 * @package   Modules\Base\Models\DbTables
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method static \Modules\Base\Models\PersonLog|array|false getRow($ids = null, $page = false, $order = false, $model = true, $cache = true)
 * @method static \Modules\Base\Models\PersonLog[]|array|false getAll($page = false, $order = false, $model = true)
 */
class PersonLog extends \Application\Assistance\DatabaseNormal
{
    /** Primary key field */
    const PERSON_LOG_ID = 'person_log_id';

    /** Person ID (FK to Person table) */
    const PERSON_ID = 'person_id';

    /** Person name (denormalized for performance) */
    const PERSON_LOG_NAME = 'person_log_name';

    /** IP address (stored as long) */
    const PERSON_LOG_IP = 'person_log_ip';

    /** Action timestamp */
    const PERSON_LOG_TIME = 'person_log_time';

    /** Module code (from Crud) */
    const PERSON_LOG_MODULE_CODE = 'person_log_module_code';

    /** Controller code (from Crud) */
    const PERSON_LOG_CONTROLLER_CODE = 'person_log_controller_code';

    /** Module name */
    const PERSON_LOG_MODULE = 'person_log_module';

    /** Controller name */
    const PERSON_LOG_CONTROLLER = 'person_log_controller';

    /** Action name */
    const PERSON_LOG_ACTION = 'person_log_action';

    /** Entity ID (the record being acted upon) */
    const PERSON_LOG_ENTITY = 'person_log_entity';

    /** Change description (before/after) */
    const PERSON_LOG_CHANGE = 'person_log_change';

    /** Archive flag or archive timestamp */
    const PERSON_LOG_ARCHIVE = 'person_log_archive';

    /** Table name */
    public static $_table = 'person_logs';

    /** Primary key field */
    public static $_index = self::PERSON_LOG_ID;

    /**
     * Field definitions.
     *
     * @var array
     */
    public static $_fields = [
        self::PERSON_LOG_ID => [],
        self::PERSON_ID => [self::FP_LINK => Person::class, self::FP_NULL => true],
        self::PERSON_LOG_NAME => [self::FP_TYPE => self::TYPE_STRING],
        self::PERSON_LOG_IP => [],
        self::PERSON_LOG_TIME => [],
        self::PERSON_LOG_MODULE_CODE => [],
        self::PERSON_LOG_CONTROLLER_CODE => [],
        self::PERSON_LOG_MODULE => [self::FP_TYPE => self::TYPE_STRING],
        self::PERSON_LOG_CONTROLLER => [self::FP_TYPE => self::TYPE_STRING],
        self::PERSON_LOG_ACTION => [self::FP_TYPE => self::TYPE_STRING],
        self::PERSON_LOG_ENTITY => [],
        self::PERSON_LOG_CHANGE => [self::FP_TYPE => self::TYPE_TEXT, self::FP_NULL => true],
        self::PERSON_LOG_ARCHIVE => [self::FP_NULL => true],
    ];

    /** Default offset for time calculations (1 day) */
    const DEFAULT_OFFSET = 86400;

    // ============================================================================
    // CHART TYPES
    // ============================================================================

    /** Chart by person */
    const CHART_PERSON = 1;

    /** Chart by page */
    const CHART_PAGE = 2;

    /** Chart by IP */
    const CHART_IP = 3;

    // ============================================================================
    // TIME STEP CONSTANTS
    // ============================================================================

    /** Minute step */
    const STEP_MINUTE = 1;

    /** Hour step */
    const STEP_HOUR = 2;

    /** Day step */
    const STEP_DAY = 3;

    // ============================================================================
    // PERIOD CONSTANTS
    // ============================================================================

    /** Day period */
    const PERIOD_DAY = 1;

    /** Week period */
    const PERIOD_WEEK = 2;

    /** Month period */
    const PERIOD_MONTH = 3;

    /** Half-year period */
    const PERIOD_HALF_YEAR = 4;

    /** Year period */
    const PERIOD_YEAR = 5;

    /**
     * Get localized period descriptions.
     *
     * @return array Period => description
     */
    public static function getPeriods()
    {
        return [
            self::PERIOD_DAY => C::locale('Day'),
            self::PERIOD_WEEK => C::locale('Week'),
            self::PERIOD_MONTH => C::locale('Month'),
            self::PERIOD_HALF_YEAR => C::locale('Quater'),
            self::PERIOD_YEAR => C::locale('Year')
        ];
    }

    /**
     * Get period grouping formats for charts.
     *
     * @return array Period => [SQL format, display format]
     */
    public static function getPeriodGroups()
    {
        return [
            self::PERIOD_DAY => ['%d-%m-%Y %H', 'd-m-Y H'],
            self::PERIOD_WEEK => ['%d-%m-%Y', 'd-m-Y'],
            self::PERIOD_MONTH => ['%u %Y', 'W Y'],
            self::PERIOD_HALF_YEAR => ['%m %Y', 'm Y'],
            self::PERIOD_YEAR => ['%m %Y', 'm Y']
        ];
    }

    /**
     * Get period offsets in seconds.
     *
     * @return array Period => seconds
     */
    public static function getOffsets()
    {
        return [
            self::PERIOD_DAY => 86400,
            self::PERIOD_WEEK => 604800,
            self::PERIOD_MONTH => 2592000,
            self::PERIOD_HALF_YEAR => 15768000,
            self::PERIOD_YEAR => 31536000
        ];
    }

    /**
     * Get step intervals for time grouping.
     *
     * @return array Step => [seconds, date format]
     */
    public static function getStepInterval()
    {
        return [
            self::STEP_MINUTE => [60, 'Y/m/d H:i:00'],
            self::STEP_HOUR => [3600, 'Y/m/d H:00'],
            self::STEP_DAY => [86400, 'Y/m/d']
        ];
    }

    /**
     * Build WHERE conditions for log filtering.
     *
     * @param \Application\Assistance\Select $select       Select object (by reference)
     * @param bool|int                       $module       Module code
     * @param bool|int                       $controller   Controller code
     * @param bool|int                       $entity       Entity ID
     * @param bool|int                       $person       Person ID
     * @param array                          $time         Time range [start, end]
     * @param bool|string                    $ip           IP address
     * @param bool|string                    $action       Action name
     * @param bool|string                    $ipMask       IP mask for range search
     * @param bool                           $onlyEntity   Only records with entity > 0
     * @param bool                           $ignoreIp     IP to exclude
     * @param bool                           $fullList     Return full list
     *
     * @return bool Whether any filters were applied
     */
    private static function getFilterWhere(
        &$select,
        $module = false,
        $controller = false,
        $entity = false,
        $person = false,
        $time = [],
        $ip = false,
        $action = false,
        $ipMask = false,
        $onlyEntity = false,
        $ignoreIp = false,
        $fullList = false
    ) {
        $check = false;

        if (false !== $module) {
            $check = true;
            $select->_where[] = static::createWhere(self::PERSON_LOG_MODULE_CODE, $module);
        }
        if (false !== $controller) {
            $check = true;
            $select->_where[] = static::createWhere(self::PERSON_LOG_CONTROLLER_CODE, $controller);
        }
        if (false !== $action) {
            $check = true;
            $select->_where[] = static::createWhere(self::PERSON_LOG_ACTION, $action);
        }
        if (false !== $person) {
            $check = true;
            $select->_where[] = static::createWhere(self::PERSON_ID, $person);
        }
        if (false !== $onlyEntity) {
            $check = true;
            $select->_where[] = static::createWhere(self::PERSON_LOG_ENTITY, 0, Where::MORE);
        } elseif (false !== $entity) {
            $check = true;
            $select->_where[] = static::createWhere(self::PERSON_LOG_ENTITY, $entity);
        }
        if (false !== $ip) {
            $check = true;
            $select->_where[] = static::createWhere(self::PERSON_LOG_IP, $ip);
        }
        if (!empty($time)) {
            $check = true;
            $select->_where[] = static::createWhere(self::PERSON_LOG_TIME, $time[0], Where::MORE_EQUAL);
            if (isset($time[1])) {
                $select->_where[] = static::createWhere(self::PERSON_LOG_TIME, $time[1] + 1, Where::LESS);
            }
        }
        if (false !== $ignoreIp) {
            $check = true;
            $select->_where[] = static::createWhere(self::PERSON_LOG_IP, $ignoreIp, Where::NOT_EQUAL);
        }
        return $check;
    }

    /** Offset for entity-to-person linking (31 days) */
    const TO_ENTITY_OFFSET = 2678400;

    /**
     * Get list of unique IP addresses from logs.
     *
     * @return \Modules\Base\Models\PersonLog[]|false
     */
    public static function getIpList()
    {
        self::setModelResponse(false);
        return (static::getSelect())
            ->setFields([(new FieldHandler())->setTable(self::_name())->setField(self::PERSON_LOG_IP)])
            ->setGroups([self::PERSON_LOG_IP])
            ->result();
    }

    /**
     * Get list of unique person IDs from logs.
     *
     * @return \Modules\Base\Models\PersonLog[]|false
     */
    public static function getPersonList()
    {
        self::setModelResponse(false);
        return (static::getSelect())
            ->setFields([(new FieldHandler())->setTable(self::_name())->setField(self::PERSON_ID)])
            ->setGroups([self::PERSON_ID])
            ->result();
    }

    /**
     * Get list of unique entities from logs.
     *
     * @return \Modules\Base\Models\PersonLog[]|false
     */
    public static function getEntityList()
    {
        self::setModelResponse(false);
        $fields = [
            self::_name() . '.' . self::PERSON_LOG_MODULE,
            self::_name() . '.' . self::PERSON_LOG_CONTROLLER,
            self::_name() . '.' . self::PERSON_LOG_ACTION,
            self::_name() . '.' . self::PERSON_LOG_ENTITY
        ];
        return (static::getSelect())
            ->setGroups($fields)
            ->order(array_fill_keys($fields, true))
            ->result();
    }

    /**
     * Get last visit times for multiple users.
     *
     * @param array $personIds Array of person IDs
     *
     * @return \Modules\Base\Models\PersonLog[]|false
     */
    public static function getLastVisit($personIds)
    {
        self::setModelResponse(false);
        return (static::getSelect())
            ->setFields([
                self::PERSON_ID,
                (new FieldHandler())->setField(self::PERSON_LOG_TIME)->setMax()->setAlias('time')
            ])
            ->setGroups([self::PERSON_ID])
            ->addWhere(static::createWhere(self::PERSON_ID, $personIds))
            ->result();
    }
}