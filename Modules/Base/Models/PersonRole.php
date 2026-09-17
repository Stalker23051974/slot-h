<?php

namespace Modules\Base\Models;

/**
 * PersonRole model class.
 *
 * Represents the many-to-many relationship between users and roles.
 *
 * @package   Modules\Base\Models
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method int getPersonRoleId()
 * @method $this setPersonRoleId(int $person_role_id)
 * @method int getPersonId()
 * @method $this setPersonId(int $person_id)
 * @method int getRoleId()
 * @method $this setRoleId(int $role_id)
 *
 * @method Person|false linkPersonId()
 * @method Role|false linkRoleId()
 */
class PersonRole extends \Application\Assistance\Model
{
    /** @var int Primary key */
    public $person_role_id;

    /** @var int Person ID */
    public $person_id;

    /** @var int Role ID */
    public $role_id;
}