<?php

namespace Modules\Base\Models;

/**
 * PersonNetwork model class.
 *
 * Represents a user's social network connection record. Stores
 * social network identities and authentication data.
 *
 * @package   Modules\Base\Models
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method int getPersonNetworkId()
 * @method $this setPersonNetworkId(int $person_network_id)
 * @method int getPersonId()
 * @method $this setPersonId(int $person_id)
 * @method string getPersonNetworkType()
 * @method $this setPersonNetworkType(string $person_network_type)
 * @method string getPersonNetworkIdentity()
 * @method $this setPersonNetworkIdentity(string $person_network_identity)
 * @method string getPersonNetworkParams()
 * @method $this setPersonNetworkParams(string $person_network_params)
 * @method int getPersonNetworkDate()
 * @method $this setPersonNetworkDate(int $person_network_date)
 *
 * @method Person|false linkPersonId()
 */
class PersonNetwork extends \Application\Assistance\Model
{
    /** @var int Primary key */
    public $person_network_id;

    /** @var int Person ID */
    public $person_id;

    /** @var string Network type (facebook, google, etc.) */
    public $person_network_type;

    /** @var string User identity on the network */
    public $person_network_identity;

    /** @var string Additional parameters (tokens, profile data) */
    public $person_network_params;

    /** @var int Connection timestamp */
    public $person_network_date;

    /** JSON data field name for protected data */
    public $_dataField = 'PersonNetworkParams';
}