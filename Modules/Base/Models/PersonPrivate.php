<?php

namespace Modules\Base\Models;

/**
 * PersonPrivate model class.
 *
 * Represents a user's private/authentication data. Stores login
 * credentials, password hash, and session identifiers.
 *
 * @package   Modules\Base\Models
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method int getPersonPrivateId()
 * @method $this setPersonPrivateId(int $person_private_id)
 * @method int getPersonId()
 * @method $this setPersonId(int $person_id)
 * @method string getPersonPrivateLogin()
 * @method $this setPersonPrivateLogin(string $person_private_login)
 * @method string getPersonPrivateNickname()
 * @method $this setPersonPrivateNickname(string $person_private_nickname)
 * @method int getPersonPrivateBanned()
 * @method $this setPersonPrivateBanned(int $person_private_banned)
 * @method string getPersonPrivateHash()
 * @method $this setPersonPrivateHash(string $person_private_hash)
 * @method string getPersonPrivateUid()
 * @method $this setPersonPrivateUid(string $person_private_uid)
 * @method string getProjectId()
 * @method $this setProjectId(string $project_id)
 *
 * @method Person|false linkPersonId()
 * @method Project|false linkProjectId()
 */
class PersonPrivate extends \Application\Assistance\Model
{
    /** @var int Primary key */
    public $person_private_id;

    /** @var int Person ID */
    public $person_id;

    /** @var string Login username */
    public $person_private_login;

    /** @var string Nickname */
    public $person_private_nickname;

    /** @var int Banned status */
    public $person_private_banned;

    /** @var string Password hash */
    public $person_private_hash;

    /** @var string Unique identifier for session tracking */
    public $person_private_uid;

    /** @var string Project ID */
    public $project_id;
}