<?php

namespace Modules\Base\Models;

/**
 * PersonProtected model class.
 *
 * Represents a user's protected/preference data. Stores user settings
 * and preferences in JSON format for flexible storage.
 *
 * @package   Modules\Base\Models
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method int getPersonProtectedId()
 * @method $this setPersonProtectedId(int $person_protected_id)
 * @method int getPersonId()
 * @method $this setPersonId(int $person_id)
 * @method string getPersonProtectedData()
 * @method $this setPersonProtectedData(string $person_protected_data)
 *
 * @method Person|false linkPersonId()
 */
class PersonProtected extends \Application\Assistance\Model
{
    /** @var int Primary key */
    public $person_protected_id;

    /** @var int Person ID */
    public $person_id;

    /** @var string Protected data (JSON) */
    public $person_protected_data;

    /** JSON data field name for protected data */
    protected $_dataField = 'PersonProtectedData';
}