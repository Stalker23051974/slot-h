<?php

/**
 * Person/user management helper.
 *
 * This class provides user-related utilities:
 * - Session management (login, logout, authentication)
 * - Role hierarchy management
 * - User cloning/copying across projects
 * - User data persistence
 * - Trigger field constants for event tracking
 *
 * The person helper is project-specific and accessed via the
 * PERSON_HELPER constant defined in the router.
 *
 * @package   Application\Helpers
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Helpers;

use \Modules\Base\Models\DbTables as db;
use \Application\Assistance\Controller\Controller as Cr;

class Person
{
    // ============================================================================
    // RESEND EMAIL STATUS CONSTANTS
    // ============================================================================

    /** Email resend successful */
    const RESEND_EMAIL_CODE_SUCCESS = 0;

    /** Email resend blocked by time limit */
    const RESEND_EMAIL_CODE_TIMELIMIT = 1;

    /** Email not found in system */
    const RESEND_EMAIL_CODE_NOT_FOUND = 2;

    /** Invalid request parameters */
    const RESEND_EMAIL_CODE_BAD_REQUEST = 3;

    /** Cookie name for storing the last visited URL */
    const LAST_URL_COOKIE = 'last_url';

    // ============================================================================
    // SESSION MANAGEMENT
    // ============================================================================

    /**
     * Clear user session and cookies.
     *
     * Logs out the current user by destroying all session data
     * and expiring authentication cookies.
     */
    public static function quit()
    {
        unset($_SESSION[Cr::API_HASH_NAME . CURRENT_PROJECT]);
        unset($_SESSION['guest' . CURRENT_PROJECT]);
        unset($_SESSION['person_id' . CURRENT_PROJECT]);
        setcookie(Cr::GOD_MODE . CURRENT_PROJECT, '', -1, '/');
        setcookie(Cr::API_HASH_NAME . CURRENT_PROJECT, '', -1, '/');
        setcookie(Person::LAST_URL_COOKIE . CURRENT_PROJECT, '', -1, '/');
    }

    /**
     * Set user authentication in session.
     *
     * @param int|string $uid       User ID
     * @param int        $projectId Project ID (default: current project)
     */
    public static function setAuth($uid, $projectId = CURRENT_PROJECT)
    {
        ini_set('session.gc_maxlifetime', 43200); // 12 hours
        ini_set('session.cookie_lifetime', 43200); // 12 hours
        $_SESSION[Cr::API_HASH_NAME . $projectId] = $uid;
    }

    // ============================================================================
    // TRIGGER FIELD CONSTANTS
    // ============================================================================

    /** Trigger field: counter value */
    const TRIGGER_FIELD_COUNTER = 'counter';

    /** Trigger field: login */
    const TRIGGER_FIELD_LOGIN = 'login';

    /** Trigger field: person name */
    const TRIGGER_FIELD_PERSON_NAME = 'person_name';

    /** Trigger field: person ID */
    const TRIGGER_FIELD_PERSON_ID = 'person_id';

    /** Trigger field: device name */
    const TRIGGER_FIELD_DEVICE_NAME = 'device_name';

    /** Trigger field: device UID */
    const TRIGGER_FIELD_DEVICE_UID = 'device_uid';

    /** Trigger field: device ID */
    const TRIGGER_FIELD_DEVICE_ID = 'device_id';

    /** Trigger field: group name */
    const TRIGGER_FIELD_GROUP_NAME = 'group_name';

    /** Trigger field: group ID */
    const TRIGGER_FIELD_GROUP_ID = 'group_id';

    /** Trigger field: customer name */
    const TRIGGER_FIELD_CUSTOMER_NAME = 'customer_name';

    /** Trigger field: customer ID */
    const TRIGGER_FIELD_CUSTOMER_ID = 'customer_id';

    /** Trigger field: layout name */
    const TRIGGER_FIELD_LAYOUT_NAME = 'layout_name';

    /** Trigger field: preset name */
    const TRIGGER_FIELD_PRESET_NAME = 'preset_name';

    /** Trigger field: campaign ID */
    const TRIGGER_FIELD_CAMPAIGN_ID = 'campaign_id';

    /** Trigger field: campaign name */
    const TRIGGER_FIELD_CAMPAIGN_NAME = 'campaign_name';

    /** Trigger field: string value */
    const TRIGGER_FIELD_STRING = 'string';

    /** Trigger field: text value */
    const TRIGGER_FIELD_TEXT = 'text';

    /** Trigger field: href/URL */
    const TRIGGER_FIELD_HREF = 'href';

    /** Trigger field: language */
    const TRIGGER_FIELD_LANGUAGE = 'language';

    /** Trigger field: email */
    const TRIGGER_FIELD_EMAIL = 'email';

    /** Trigger field: phone number */
    const TRIGGER_FIELD_PHONE = 'phone';

    /** Trigger field: date */
    const TRIGGER_FIELD_DATE = 'date';

    /** Trigger field: name */
    const TRIGGER_FIELD_NAME = 'name';

    // ============================================================================
    // ROLE HIERARCHY
    // ============================================================================

    /**
     * Get the complete role hierarchy.
     *
     * Builds a tree structure of roles with their parent-child relationships.
     * Used for access control and permission inheritance.
     *
     * @return array Role access structure: [role_id => [children, role_object]]
     */
    public static function getRoles()
    {
        $rolesAccess = [];
        $res = db\RoleRole::getAll(false, [db\RoleRole::PARENT]);

        foreach ($res as $k => $v) {
            $rolesAccess[$v->getRoleId()] = [
                [],
                $v
            ];

            if ($v->getRoleId() > 0) {
                $v0 = $v;
                while ($v0->getParent() > 0) {
                    $rolesAccess[$v0->getParent()][0][] = $v->getRoleId();
                    $v0 = $rolesAccess[$v0->getParent()][1];
                }
            }
        }
        return $rolesAccess;
    }

    // ============================================================================
    // USER CLONING
    // ============================================================================

    /**
     * Clone a user to another project.
     *
     * Copies all user data including:
     * - Personal information (name, age, gender, etc.)
     * - Authentication credentials (login, hash, UID)
     * - Protected data (preferences, settings)
     * - Entity reference
     * - Role assignment
     * - Permission rules
     *
     * @param int|string $personId  Source user ID
     * @param int|string $roleId    Role ID for the cloned user in the new project
     * @param int|string $projectId Target project ID
     *
     * @return int|string|false New user ID or false if login already exists
     */
    public static function clonePerson($personId, $roleId, $projectId)
    {
        // Get source person data
        $person = db\Person::getRow($personId)->setProjectId($projectId);
        $data = $person->lotsPersonPrivateInBaseByPersonId();
        $id = false;

        /** @var \Modules\Base\Models\PersonPrivate $data */

        // Check if user with same login already exists in target project
        if (!db\PersonPrivate::getByLogin($data->getPersonPrivateLogin(), $projectId)) {
            // Create new person record
            $id = (new \Modules\Base\Models\Person())
                ->setPersonFirstName($person->getPersonFirstName())
                ->setPersonSecondName($person->getPersonSecondName())
                ->setPersonPatronymic($person->getPersonPatronymic())
                ->setCountryId($person->getCountryId())
                ->setCityName($person->getCityName())
                ->setPersonAge((int)($person->getPersonAge()))
                ->setPersonMale($person->getPersonMale())
                ->setPersonStatus($person->getPersonStatus())
                ->setParent($personId)
                ->setLanguageId($person->getLanguageId())
                ->setPersonNotification($person->getPersonNotification())
                ->setTimezoneId($person->getTimezoneId())
                ->save($projectId);

            // Create new private data (auth credentials)
            (new \Modules\Base\Models\PersonPrivate())
                ->setPersonId($id)
                ->setPersonPrivateBanned($data->getPersonPrivateBanned())
                ->setPersonPrivateHash($data->getPersonPrivateHash())
                ->setPersonPrivateUid(md5($data->getPersonPrivateUid() . time()))
                ->setPersonPrivateLogin($data->getPersonPrivateLogin())
                ->setProjectId($projectId)
                ->save($projectId);

            // Copy protected data (preferences, settings)
            $data = $person->lotsPersonProtectedInBaseByPersonId();
            /** @var \Modules\Base\Models\PersonProtected $data */
            (new \Modules\Base\Models\PersonProtected())
                ->setPersonId($id)
                ->setPersonProtectedData($data->getPersonProtectedData())
                ->save();

            // Copy entity reference
            $data = db\Entity::getByEntity($personId, db\PersonPrivate::$_table);
            /** @var \Modules\Base\Models\Entity $data */
            (new \Modules\Base\Models\Entity())
                ->setEntityEntity($id)
                ->setEntityEntityType($data->getEntityEntityType())
                ->setEntityValue($data->getEntityValue())
                ->save();

            // Assign role to new user
            (new \Modules\Base\Models\PersonRole())
                ->setPersonId($id)
                ->setRoleId($roleId)
                ->save();

            // Copy role rules to user rules
            if ($rules = db\RoleRule::getByRole($roleId)) {
                foreach ($rules as $v) {
                    (new \Modules\Base\Models\PersonRule())
                        ->setRuleId($v->getRuleId())
                        ->setPersonId($id)
                        ->save();
                }
            }
        }
        return $id;
    }
}