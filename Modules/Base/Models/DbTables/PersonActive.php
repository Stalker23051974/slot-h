<?php

namespace Modules\Base\Models\DbTables;

use \Modules\Base\Models\DbTables as db;

/**
 * PersonActive database table class.
 *
 * Manages online user presence tracking. Records user sessions and
 * their last activity time to determine who is currently online.
 *
 * Features:
 * - Session-based tracking
 * - Automatic cleanup of stale records
 * - Online user list generation
 * - TTL-based expiration (via ONLINE_LIFETIME constant)
 *
 * @package   Modules\Base\Models\DbTables
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method static \Modules\Base\Models\PersonActive|array|false getRow($ids = null, $page = false, $order = false, $model = true, $cache = true)
 */
class PersonActive extends \Application\Assistance\DatabaseNormal
{
    /** Primary key field */
    const PERSON_ACTIVE_ID = 'person_active_id';

    /** Person ID (FK to Person table) */
    const PERSON_ID = 'person_id';

    /** Last activity timestamp */
    const PERSON_ACTIVE_DATE = 'person_active_date';

    /** Session identifier */
    const PERSON_ACTIVE_SESSION = 'person_active_session';

    /** Table name */
    public static $_table = 'person_actives';

    /** Primary key field */
    public static $_index = self::PERSON_ACTIVE_ID;

    /**
     * Field definitions.
     *
     * @var array
     */
    public static $_fields = [
        self::PERSON_ACTIVE_ID => [],
        self::PERSON_ID => [self::FP_INDEX => true, self::FP_LINK => Person::class, self::FP_NULL => true],
        self::PERSON_ACTIVE_DATE => [self::FP_INDEX => true],
        self::PERSON_ACTIVE_SESSION => [self::FP_TYPE => self::TYPE_STRING],
    ];

    /**
     * Get active records by person ID.
     *
     * Removes old records before querying.
     *
     * @param int|int[] $personId Person ID(s)
     *
     * @return \Modules\Base\Models\PersonActive|\Modules\Base\Models\PersonActive[]|false
     */
    public static function getByPerson($personId)
    {
        self::removeOld();
        return (static::getSelect())
            ->addWhere(static::createWhere(self::PERSON_ID, $personId))
            ->pop(!is_array($personId))
            ->result();
    }

    /**
     * Get active record by session ID.
     *
     * Removes old records before querying.
     *
     * @param string $session Session identifier
     *
     * @return \Modules\Base\Models\PersonActive|false
     */
    public static function getBySession($session)
    {
        self::removeOld();
        return (static::getSelect())
            ->addWhere(static::createWhere(self::PERSON_ACTIVE_SESSION, $session))
            ->pop()
            ->result();
    }

    /**
     * Get all active records (overrides parent).
     *
     * Removes old records before fetching.
     *
     * @param bool|int   $page  Page number
     * @param bool|array $order Order by clause
     * @param bool       $model Return Model objects
     *
     * @return \Modules\Base\Models\PersonActive[]|false
     */
    public static function getAll($page = false, $order = false, $model = true)
    {
        self::removeOld();
        self::setModelResponse($model);
        return parent::getAll($page, $order);
    }

    /**
     * Get list of currently online user IDs.
     *
     * @return array|false Array of user IDs or false on failure
     */
    public static function getOnline()
    {
        if ($res = self::getAll(false, false, false)) {
            return dfArrayKeys($res);
        }
        return false;
    }

    /**
     * Remove stale/expired active records.
     *
     * Deletes records older than ONLINE_LIFETIME constant.
     *
     * @return array|false Deletion result
     */
    protected static function removeOld()
    {
        return (static::getSelect(\Application\Assistance\Select::DELETE))
            ->addWhere(static::createWhere(
                self::PERSON_ACTIVE_DATE,
                (CURRENT_TIME - \Config\CC::constant(db\Constant::ONLINE_LIFETIME)),
                \Application\Assistance\Select\Where::LESS_EQUAL
            ))
            ->result();
    }
}