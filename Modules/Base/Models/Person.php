<?php

namespace Modules\Base\Models;

use Config\CC as C;
use \Modules\Base\Models\DbTables as db;

/**
 * Person model class.
 *
 * Represents a user/person record. This is the core user model
 * that handles user data, permissions, roles, and authentication.
 *
 * @package   Modules\Base\Models
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method int getPersonId()
 * @method $this setPersonId(int $person_id)
 * @method string getPersonFirstName()
 * @method $this setPersonFirstName(string $person_first_name)
 * @method string getPersonSecondName()
 * @method $this setPersonSecondName(string $person_second_name)
 * @method string getPersonPatronymic()
 * @method $this setPersonPatronymic(string $person_patronymic)
 * @method int getCountryId()
 * @method $this setCountryId(int $country_id)
 * @method int getCityName()
 * @method $this setCityName(int $city_id)
 * @method string getPersonAge()
 * @method $this setPersonAge(string $person_age)
 * @method int getPersonMale()
 * @method $this setPersonMale(int $person_male)
 * @method int getPersonStatus()
 * @method $this setPersonStatus(int $person_status)
 * @method int getProjectId()
 * @method $this setProjectId(int $project_id)
 * @method int getParent()
 * @method $this setParent(int $parent)
 * @method int getLanguageId()
 * @method $this setLanguageId(int $language_id)
 * @method int getVoiceId()
 * @method $this setVoiceId(int $voice_id)
 * @method int getPersonNotification()
 * @method $this setPersonNotification(int $person_notification)
 * @method int getTimezoneId()
 * @method $this setTimezoneId(int $timezone_id)
 *
 * @method string getUpdatedAt()
 *
 * @method PersonPrivate|false lotsPersonPrivateInBaseByPersonId()
 * @method PersonProtected|false lotsPersonProtectedInBaseByPersonId()
 * @method PersonRole|false lotsPersonRoleInBaseByPersonId()
 * @method PersonRule[]|false relatedPersonRuleInBaseByPersonId()
 * @method Person|false linkParent()
 * @method Project|false linkProjectId()
 * @method \Modules\Geo\Models\Language|false linkLanguageId()
 * @method \Modules\Geo\Models\Timezone|false linkTimezoneId()
 */
class Person extends \Application\Assistance\Model
{
    /** @var int Primary key */
    public $person_id;

    /** @var string First name */
    public $person_first_name;

    /** @var string Last name/surname */
    public $person_second_name;

    /** @var string Patronymic/middle name */
    public $person_patronymic;

    /** @var int Country ID */
    public $country_id;

    /** @var int City ID */
    public $city_id;

    /** @var string Date of birth */
    public $person_age;

    /** @var int Gender */
    public $person_male;

    /** @var int User status */
    public $person_status;

    /** @var int Project ID */
    public $project_id;

    /** @var int Parent user ID */
    public $parent;

    /** @var int Language ID */
    public $language_id;

    /** @var int Voice ID */
    public $voice_id;

    /** @var int Notification preference */
    public $person_notification;

    /** @var int Timezone ID */
    public $timezone_id;

    /** @var string City name (denormalized) */
    public $city_name;

    /** @var array User permission rules */
    public $_rules;

    /** @var array Role IDs cache */
    private $_roleIds = [];

    /** @var bool Superadmin flag */
    public $_s_a = false;

    /** @var bool Partner supervisor flag */
    public $_p_s = false;

    /**
     * Get the user's full name.
     *
     * @param bool $default Default value if name is empty
     * @param bool $voice   Include patronymic
     * @param bool $short   Return only first name
     *
     * @return bool|string
     */
    public function _name($default = false, $voice = false, $short = false)
    {
        $name = dfTrim(implode(' ', false === $short
            ? (false === $voice
                ? [$this->getPersonFirstName(), $this->getPersonPatronymic(), $this->getPersonSecondName()]
                : [$this->getPersonFirstName(), $this->getPersonPatronymic()])
            : [$this->getPersonFirstName()]));

        if (!empty($name)) {
            return $name;
        }
        if (false !== $default) {
            return $default;
        }
        return !empty($name)
            ? $name
            : (false === $default
                ? $this->lotsPersonPrivateInBaseByPersonId()->getPersonPrivateLogin() . ' (#' . $this->_id() . ')'
                : $default);
    }

    /**
     * Get short name (first letter of each name part).
     *
     * @return string
     */
    public function _shortName()
    {
        $name = '';
        foreach ([$this->getPersonSecondName(), $this->getPersonFirstName(), $this->getPersonPatronymic()] as $v) {
            if ($v != '') {
                $name .= empty($name) ? $v . ' ' : $v . ' ';
            }
        }
        return $name;
    }

    /**
     * Get initials for chat display.
     *
     * @return string
     */
    public function getShortToChat()
    {
        $res = [];
        if (dfTrim($this->getPersonFirstName()) !== '') {
            $res[] = strtoupper(mb_substr(dfTrim($this->getPersonFirstName()), 0, 1));
        }
        if (dfTrim($this->getPersonSecondName()) !== '') {
            $res[] = strtoupper(mb_substr(dfTrim($this->getPersonSecondName()), 0, 1));
        }
        return dfCount($res) == 0 ? 'U' : implode('', $res);
    }

    /**
     * Set user permission rules.
     *
     * @param array $rules Permission rules
     */
    public function setRules($rules)
    {
        $this->_rules = $rules;
    }

    /**
     * Check if the user has a specific rule.
     *
     * @param int|int[] $rule Rule ID or array of rule IDs
     *
     * @return bool
     */
    public function checkRules($rule)
    {
        return $rule > 0
            ? dfArrayIntersect(is_array($rule) ? $rule : [$rule], $this->_rules['_rules'])
            : false;
    }

    /**
     * Get child roles of the user.
     *
     * @return mixed
     */
    public function getChildRoles()
    {
        return $this->_rules['_map'];
    }

    /**
     * Get user's rules.
     *
     * @return mixed
     */
    public function getRules()
    {
        return $this->_rules['_rules'];
    }

    /**
     * Check if user is super admin.
     *
     * @return bool
     */
    public function isSA()
    {
        return $this->_s_a;
    }

    /**
     * Check if user is partner supervisor.
     *
     * @return bool
     */
    public function isPS()
    {
        return $this->_p_s;
    }

    /**
     * Set user permissions based on role and rules.
     *
     * Builds the complete permission set including inherited roles
     * and user-specific rule overrides.
     *
     * @param \Application\Assistance\View\View $view View object (unused)
     */
    public function setPersonPermissions()
    {
        $currentRules = false;

        if ($roleRes = $this->lotsPersonRoleInBaseByPersonId()) {
            $currentRules = db\Role::getChilds($roleRes->getRoleId());
            $eventRes = dfJsonDecode($currentRules[db\Role::ROLE_DATA], true);
            $currentRules[db\Role::ROLE_DATA] = ($eventRes && isset($eventRes[db\Role::DATA_TASK_EVENTS]))
                ? $eventRes[db\Role::DATA_TASK_EVENTS]
                : [];
        }

        $this->getRoleIds($currentRules['_list']);
        $currentRules['_map'] = $this->_roleIds;

        $rules = [];
        if ($rulesRes = $this->relatedPersonRuleInBaseByPersonId()) {
            foreach ($rulesRes as $v) {
                $rules[] = $v->getRuleId();
            }
        }

        $currentRules['_rules'] = $rules;
        $currentRules[db\Role::ROLE_ID] = $roleRes->getRoleId();
        $this->setRules($currentRules);
        $this->_s_a = $this->_rules[db\Role::ROLE_ID] == C::constant(db\Constant::S_A_ROLE);
    }

    /**
     * Recursively collect role IDs from the role hierarchy.
     *
     * @param array $list Role list from the hierarchy
     */
    private function getRoleIds($list)
    {
        foreach ($list as $k => $v) {
            $this->_roleIds[] = $k;
            if (false !== $v['_list']) {
                $this->getRoleIds($v['_list']);
            }
        }
    }
}