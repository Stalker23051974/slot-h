<?php

namespace Modules\Base\Models;

/**
 * PersonActive model class.
 *
 * Represents an active user session record. Tracks online users
 * and their session activity for presence monitoring.
 *
 * @package   Modules\Base\Models
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method int getPersonActiveId()
 * @method $this setPersonActiveId(int $person_active_id)
 * @method int getPersonId()
 * @method $this setPersonId(int $person_id)
 * @method int getPersonActiveDate()
 * @method $this setPersonActiveDate(int $person_active_date)
 * @method string getPersonActiveSession()
 * @method $this setPersonActiveSession(string $person_active_session)
 *
 * @method Person|false linkPersonId()
 */
class PersonActive extends \Application\Assistance\Model
{
    /** @var int Primary key */
    public $person_active_id;

    /** @var int Person ID */
    public $person_id;

    /** @var int Last activity timestamp */
    public $person_active_date;

    /** @var string Session identifier */
    public $person_active_session;
}