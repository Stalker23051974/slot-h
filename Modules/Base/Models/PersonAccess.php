<?php

namespace Modules\Base\Models;

/**
 * PersonAccess model class.
 *
 * Represents a user access link record. Stores direct access links
 * that allow users to access specific resources without additional
 * authentication.
 *
 * @package   Modules\Base\Models
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method int getPersonAccessId()
 * @method $this setPersonAccessId(int $person_access_id)
 * @method int getPersonId()
 * @method $this setPersonId(int $person_id)
 * @method int getPersonAccessLink()
 * @method $this setPersonAccessLink(string $person_access_link)
 *
 * @method Person|false linkPersonId()
 */
class PersonAccess extends \Application\Assistance\Model
{
    /** @var int Primary key */
    public $person_access_id;

    /** @var int Person ID */
    public $person_id;

    /** @var string Access link URL */
    public $person_access_link;
}