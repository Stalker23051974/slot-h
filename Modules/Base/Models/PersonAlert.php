<?php

namespace Modules\Base\Models;

/**
 * PersonAlert model class.
 *
 * Represents a user alert record. Stores notifications and alerts
 * for users about various system events.
 *
 * @package   Modules\Base\Models
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method int getPersonAlertId()
 * @method $this setPersonAlertId(int $person_alert_id)
 * @method int getPersonId()
 * @method $this setPersonId(int $person_id)
 * @method int getPersonAlertType()
 * @method $this setPersonAlertType(int $person_alert_type)
 * @method int getPersonAlertTime()
 * @method $this setPersonAlertTime(int $person_alert_time)
 * @method string getPersonAlertData()
 * @method $this setPersonAlertData(string $person_alert_data)
 *
 * @method Person|false linkPersonId()
 */
class PersonAlert extends \Application\Assistance\Model
{
    /** @var int Primary key */
    public $person_alert_id;

    /** @var int Person ID */
    public $person_id;

    /** @var int Alert type */
    public $person_alert_type;

    /** @var int Alert timestamp */
    public $person_alert_time;

    /** @var string Alert data (JSON) */
    public $person_alert_data;

    /** JSON data field name for protected data */
    protected $_dataField = 'PersonAlertData';
}