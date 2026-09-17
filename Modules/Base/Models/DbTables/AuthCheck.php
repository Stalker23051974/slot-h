<?php

namespace Modules\Base\Models\DbTables;

use \Modules\Base\Models\DbTables as db;

/**
 * AuthCheck database table class.
 *
 * Manages authentication attempt tracking for brute force protection.
 * Records login attempts and limits the number of retries within
 * a configurable time window.
 *
 * Features:
 * - Tracks authentication attempts by server fingerprint hash
 * - Auto-cleans old records (beyond TTL)
 * - Provides attempt count for rate limiting
 *
 * @package   Modules\Base\Models\DbTables
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method static \Modules\Base\Models\AuthCheck|array|false getRow($ids = null, $page = false, $order = false, $model = true, $cache = true)
 * @method static \Modules\Base\Models\AuthCheck[]|array|false getAll($page = false, $order = false, $model = true)
 */
class AuthCheck extends \Application\Assistance\DatabaseNormal
{
    /** Primary key field */
    const AUTH_CHECK_ID = 'auth_check_id';

    /** Timestamp of the attempt */
    const AUTH_CHECK_TIME = 'auth_check_time';

    /** Server fingerprint hash (identifies the client) */
    const AUTH_CHECK_HASH = 'auth_check_hash';

    /** Number of attempts made */
    const AUTH_CHECK_ATTEMPT = 'auth_check_attempt';

    /** Table name */
    public static $_table = 'auth_checks';

    /** Primary key field */
    public static $_index = self::AUTH_CHECK_ID;

    /**
     * Field definitions.
     *
     * @var array
     */
    public static $_fields = [
        self::AUTH_CHECK_ID => [],
        self::AUTH_CHECK_TIME => [self::FP_INDEX => true],
        self::AUTH_CHECK_HASH => [self::FP_TYPE => self::TYPE_STRING, self::FP_INDEX => true],
        self::AUTH_CHECK_ATTEMPT => []
    ];

    /**
     * Check authentication attempts by server hash.
     *
     * Removes expired attempts (older than AUTH_TIMEOUT constant),
     * then returns the current attempt record for the client.
     *
     * @return \Modules\Base\Models\AuthCheck|false AuthCheck model or false if none
     */
    public static function checkByHash()
    {
        $ttl = \Config\CC::constant(db\Constant::AUTH_TIMEOUT);

        // Clean up expired attempts
        (self::getSelect(\Application\Assistance\Select::DELETE))
            ->addWhere(self::createWhere(self::AUTH_CHECK_TIME, CURRENT_TIME - $ttl, \Application\Assistance\Select\Where::LESS))
            ->result();

        // Get current client's attempt record
        return (self::getSelect())
            ->addWhere(self::createWhere(self::AUTH_CHECK_HASH, \Application\Helpers\Line::getServerHash()))
            ->pop()
            ->result();
    }
}