<?php

namespace Modules\Geo\Models\DbTables;

use \Application\Assistance\Select\Where;

/**
 * Timezone database table class.
 *
 * Manages timezone records with their offsets and codes.
 * Stores timezone information for user and project time settings.
 *
 * Features:
 * - Timezone identifier and standard name
 * - GMT offset for calculations
 * - Timezone code for display
 * - Listing template for dropdowns
 *
 * @package   Modules\Geo\Models\DbTables
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method static \Modules\Geo\Models\Timezone|array|false getRow($ids = null, $page = false, $order = false, $model = true, $cache = true)
 * @method static \Modules\Geo\Models\Timezone[]|array|false getAll($page = false, $order = false, $model = true)
 */
class Timezone extends \Application\Assistance\DatabaseNormal
{
    /** Primary key field */
    const TIMEZONE_ID = 'timezone_id';

    /** Standard timezone name */
    const TIMEZONE_STANDART = 'timezone_standart';

    /** Display value */
    const TIMEZONE_VALUE = 'timezone_value';

    /** GMT offset in hours */
    const TIMEZONE_OFFSET = 'timezone_offset';

    /** Timezone code (e.g., UTC, EST, etc.) */
    const TIMEZONE_CODE = 'timezone_code';

    /** Table name */
    public static $_table = 'timezones';

    /** Database name */
    public static $_base = 'geo';

    /** Primary key field */
    public static $_index = self::TIMEZONE_ID;

    /**
     * Field definitions.
     *
     * @var array
     */
    public static $_fields = [
        self::TIMEZONE_ID => [self::FP_TYPE => self::TYPE_STRING],
        self::TIMEZONE_STANDART => [self::FP_TYPE => self::TYPE_STRING],
        self::TIMEZONE_VALUE => [self::FP_TYPE => self::TYPE_STRING],
        self::TIMEZONE_OFFSET => [self::FP_TYPE => self::TYPE_FLOAT],
        self::TIMEZONE_CODE => [self::FP_TYPE => self::TYPE_STRING],
    ];

    /**
     * List template for timezones.
     *
     * @var array
     */
    protected static $_listTemplate = [
        self::LIST_TEMPLATE_BASE => ['id' => Timezone::TIMEZONE_ID, 'value' => Timezone::TIMEZONE_VALUE]
    ];

    /**
     * Get main/primary timezones (with non-null code).
     *
     * @return \Modules\Geo\Models\Timezone[]|bool
     */
    public static function getMain()
    {
        return (static::getSelect())
            ->addWhere(static::createWhere(self::TIMEZONE_CODE, null, Where::NOT_NULL))
            ->order(self::TIMEZONE_CODE . ' ' . \Application\Assistance\Select::SORT_DESC)
            ->result();
    }
}