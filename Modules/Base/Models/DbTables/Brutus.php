<?php

namespace Modules\Base\Models\DbTables;

/**
 * Brutus database table class.
 *
 * Manages brute force attack detection and logging.
 * Records suspicious URL access attempts and tracks their frequency
 * to identify and block potential attackers.
 *
 * Features:
 * - Logs unauthorized URL access attempts
 * - Tracks attempt counters per URL
 * - Provides data for security monitoring and blocking
 *
 * @package   Modules\Base\Models\DbTables
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method static \Modules\Base\Models\Brutus|array|false getRow($ids = null, $page = false, $order = false, $model = true, $cache = true)
 * @method static \Modules\Base\Models\Brutus[]|array|false getAll($page = false, $order = false, $model = true)
 */
class Brutus extends \Application\Assistance\DatabaseNormal
{
    /** Primary key field */
    const BRUTUS_ID = 'brutus_id';

    /** Accessed URL path */
    const BRUTUS_URL = 'brutus_url';

    /** Number of access attempts */
    const BRUTUS_COUNTER = 'brutus_counter';

    /** Table name */
    public static $_table = 'brutus';

    /** Primary key field */
    public static $_index = self::BRUTUS_ID;

    /**
     * Field definitions.
     *
     * @var array
     */
    public static $_fields = [
        self::BRUTUS_ID => [self::FP_INDEX => true],
        self::BRUTUS_URL => [self::FP_TYPE => self::TYPE_STRING],
        self::BRUTUS_COUNTER => [self::FP_NULL => true],
    ];

    /**
     * Get a Brutus record by URL.
     *
     * @param string $url The URL path to look up
     *
     * @return \Modules\Base\Models\Brutus|false Brutus model or false if not found
     */
    public static function getByUrl($url)
    {
        return (static::getSelect())
            ->addWhere(static::createWhere(self::BRUTUS_URL, $url))
            ->pop()
            ->result();
    }
}