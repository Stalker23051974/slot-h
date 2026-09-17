<?php

namespace Modules\Base\Models\DbTables;

/**
 * PersonNetwork database table class.
 *
 * Manages social network connections for users. Stores information about
 * which social networks a user is connected to and their identities
 * on those platforms.
 *
 * Features:
 * - Multiple network support (Facebook, Google, VK, etc.)
 * - Identity storage (user ID on the external platform)
 * - Additional parameters storage (tokens, profile data)
 * - Timestamp tracking for connections
 * - Person-based lookup
 *
 * @package   Modules\Base\Models\DbTables
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method static \Modules\Base\Models\PersonNetwork|array|false getRow($ids = null, $page = false, $order = false, $model = true, $cache = true)
 * @method static \Modules\Base\Models\PersonNetwork[]|array|false getAll($page = false, $order = false, $model = true)
 */
class PersonNetwork extends \Application\Assistance\DatabaseNormal
{
    /** Primary key field */
    const PERSON_NETWORK_ID = 'person_network_id';

    /** Person ID (FK to Person table) */
    const PERSON_ID = 'person_id';

    /** Network type (facebook, google, vk, etc.) */
    const PERSON_NETWORK_TYPE = 'person_network_type';

    /** User identity on the network (external ID) */
    const PERSON_NETWORK_IDENTITY = 'person_network_identity';

    /** Additional parameters (token, profile data, etc.) */
    const PERSON_NETWORK_PARAMS = 'person_network_params';

    /** Connection timestamp */
    const PERSON_NETWORK_DATE = 'person_network_date';

    /** Table name */
    public static $_table = 'person_networks';

    /** Primary key field */
    public static $_index = self::PERSON_NETWORK_ID;

    /**
     * Field definitions.
     *
     * @var array
     */
    public static $_fields = [
        self::PERSON_NETWORK_ID => [],
        self::PERSON_ID => [self::FP_INDEX => true, self::FP_LINK => Person::class],
        self::PERSON_NETWORK_TYPE => [self::FP_TYPE => self::TYPE_STRING],
        self::PERSON_NETWORK_IDENTITY => [self::FP_TYPE => self::TYPE_STRING],
        self::PERSON_NETWORK_PARAMS => [self::FP_TYPE => self::TYPE_STRING],
        self::PERSON_NETWORK_DATE => [],
    ];

    /**
     * Get network connections for a person.
     *
     * @param int  $personId Person ID
     * @param bool $exclude  Reserved for future use
     *
     * @return \Modules\Base\Models\PersonNetwork[]|false
     */
    public static function getByPersonId($personId, $exclude = false)
    {
        return (self::getSelect())
            ->addWhere(self::createWhere(self::PERSON_ID, $personId))
            ->result();
    }

    /**
     * Get a network connection by network type and identity.
     *
     * @param string $network Network type (facebook, google, etc.)
     * @param int    $id      External user ID
     *
     * @return \Modules\Base\Models\PersonNetwork|false
     */
    public static function getByNetwork($network, $id)
    {
        return (self::getSelect())
            ->addWhere([
                self::createWhere(self::PERSON_NETWORK_TYPE, $network),
                self::createWhere(self::PERSON_NETWORK_IDENTITY, $id)
            ])
            ->pop()
            ->result();
    }
}