<?php

namespace Modules\Base\Models\DbTables;

use Application\Assistance\Select\FieldHandler;
use Application\Assistance\Select\JoinField;
use \Application\Assistance\Select\Where as Where;
use Application\Helpers\Database;
use \Config\CC as C;

/**
 * Person database table class.
 *
 * Manages user/person records in the system. This is the core user
 * management table that stores all user information.
 *
 * Features:
 * - Project isolation via DatabaseExtendProject
 * - Soft delete support via DatabaseExtend
 * - Personal information (name, age, gender, etc.)
 * - Location data (country, city)
 * - Preferences (language, timezone, voice)
 * - Status management (waiting, active, banned, deleted)
 * - Role and rule relationships
 * - Parent-child relationships (for user hierarchies)
 *
 * @package   Modules\Base\Models\DbTables
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method static \Modules\Base\Models\Person|array|false getRow($ids = null, $page = false, $order = false, $model = true, $cache = true)
 * @method static \Modules\Base\Models\Person[]|array|false getAll($page = false, $order = false, $model = true)
 */
class Person extends \Application\Assistance\DatabaseExtendProject
{
    /** Primary key field */
    const PERSON_ID = 'person_id';

    /** First name */
    const PERSON_FIRST_NAME = 'person_first_name';

    /** Last name/surname */
    const PERSON_SECOND_NAME = 'person_second_name';

    /** Patronymic/middle name */
    const PERSON_PATRONYMIC = 'person_patronymic';

    /** Country ID (FK to Country table) */
    const COUNTRY_ID = 'country_id';

    /** City name (denormalized for performance) */
    const CITY_NAME = 'city_name';

    /** Age/date of birth */
    const PERSON_AGE = 'person_age';

    /** Gender (MAN = 1, WOMAN = 2) */
    const PERSON_MALE = 'person_male';

    /** User status (waiting, active, banned, deleted) */
    const PERSON_STATUS = 'person_status';

    /** Parent user ID (for hierarchy) */
    const PARENT = 'parent';

    /** Language ID (FK to Language table) */
    const LANGUAGE_ID = 'language_id';

    /** Voice ID (FK to Voice table) */
    const VOICE_ID = 'voice_id';

    /** Notification preference */
    const PERSON_NOTIFICATION = 'person_notification';

    /** Timezone ID (FK to Timezone table) */
    const TIMEZONE_ID = 'timezone_id';

    /** Enable project extend */
    public static $_projectExtend = true;

    /** Table name */
    public static $_table = 'persons';

    /** Primary key field */
    public static $_index = self::PERSON_ID;

    /**
     * Field definitions.
     *
     * @var array
     */
    public static $_fields = [
        self::PERSON_ID => [self::FP_INDEX => true],
        self::PERSON_FIRST_NAME => [self::FP_TYPE => self::TYPE_STRING, self::FP_NULL => true, self::FP_LENGTH => 64],
        self::PERSON_SECOND_NAME => [self::FP_TYPE => self::TYPE_STRING, self::FP_NULL => true, self::FP_LENGTH => 64],
        self::PERSON_PATRONYMIC => [self::FP_TYPE => self::TYPE_STRING, self::FP_NULL => true, self::FP_LENGTH => 64],
        self::COUNTRY_ID => [self::FP_NULL => true],
        self::CITY_NAME => [self::FP_NULL => true, self::FP_TYPE => self::TYPE_STRING, self::FP_LENGTH => 128],
        self::PERSON_AGE => [self::FP_TYPE => self::TYPE_DATE, self::FP_NULL => true],
        self::PERSON_MALE => [self::FP_NULL => true, self::FP_INDEX => true],
        self::PERSON_STATUS => [self::FP_NULL => true, self::FP_INDEX => true],
        self::PARENT => [self::FP_LINK => Person::class, self::FP_NULL => true],
        self::LANGUAGE_ID => [self::FP_LINK => \Modules\Geo\Models\DbTables\Language::class],
        self::VOICE_ID => [self::FP_NULL => true],
        self::PERSON_NOTIFICATION => [self::FP_NULL => true],
        self::TIMEZONE_ID => [self::FP_LINK => \Modules\Geo\Models\DbTables\Timezone::class, self::FP_NULL => true],
    ];

    /**
     * Set fields to be logged with friendly names.
     */
    public static function setFieldsToLog()
    {
        self::$_fieldsToLog = [
            self::PERSON_FIRST_NAME => C::locale('Name'),
            self::PERSON_SECOND_NAME => C::locale('Surname'),
            self::PERSON_PATRONYMIC => C::locale('Patronymic'),
            self::COUNTRY_ID => C::locale('Country'),
            self::CITY_NAME => C::locale('City'),
            self::PERSON_AGE => C::locale('Date of birth'),
            self::PERSON_MALE => C::locale('Gender'),
        ];
    }

    /**
     * Get empty/guest person name.
     *
     * @return string Localized "User"
     */
    public static function getEmptyPersonName()
    {
        return C::locale('User');
    }

    // ============================================================================
    // STATUS CONSTANTS
    // ============================================================================

    /** Waiting for confirmation */
    const STATUS_WAITING = 1;

    /** Active/enabled user */
    const STATUS_ENABLE = 2;

    /** Banned/disabled user */
    const STATUS_BANNED = 3;

    /** Deleted account */
    const STATUS_DELETED = 4;

    /**
     * Get localized status descriptions.
     *
     * @param bool|int $status Specific status code
     *
     * @return array|string Status list or specific status description
     */
    public static function getStatuses($status = false)
    {
        $list = [
            self::STATUS_WAITING => C::locale('Not confirmed'),
            self::STATUS_ENABLE => C::locale('Active'),
            self::STATUS_BANNED => C::locale('Disconnected'),
            self::STATUS_DELETED => C::locale('Account deleted'),
        ];
        return false === $status ? $list : (isset($list[$status]) ? $list[$status] : '');
    }

    /**
     * Get active statuses.
     *
     * @return array
     */
    public static function getActiveStatuses()
    {
        return [
            self::STATUS_ENABLE,
        ];
    }

    /**
     * Get banned statuses.
     *
     * @return array
     */
    public static function getBannedStatuses()
    {
        return [
            self::STATUS_BANNED,
        ];
    }

    // ============================================================================
    // GENDER CONSTANTS
    // ============================================================================

    /** Male gender */
    const MAN = 1;

    /** Female gender */
    const WOMAN = 2;

    /**
     * Get localized gender descriptions.
     *
     * @param bool|int $gender Specific gender code
     * @param bool     $form   Return as form options
     *
     * @return array|string Gender list or specific gender description
     */
    public static function getMale($gender = false, $form = false)
    {
        $male = C::locale('Male');
        $female = C::locale('Female');
        $list = [
            self::MAN => false === $form ? $male : ['id' => self::MAN, 'value' => $male],
            self::WOMAN => false === $form ? $female : ['id' => self::WOMAN, 'value' => $female]
        ];
        return true === $form ? $list : (false === $gender ? $list : (isset($list[$gender]) ? $list[$gender] : ''));
    }

    // ============================================================================
    // FILTER CONSTANTS
    // ============================================================================

    /** Filter by name */
    const FILTER_NAME = '__name';

    /** Filter by email */
    const FILTER_EMAIL = '__email';

    /** Filter by role */
    const FILTER_ROLE = '__role';

    /** Filter by city */
    const FILTER_CITY = '__city';

    /** Filter by disabled status */
    const FILTER_DISABLED = '__disabled';

    /** Filter by project */
    const FILTER_PROJECT = '__project';

    /** Filter by customer/partner */
    const FILTER_CUSTOMER = '__partner';

    /** Filter by language */
    const FILTER_LANGUAGE = '__language';

    /** Filter by country */
    const FILTER_COUNTRY = '__country';

    /** Filter by status */
    const FILTER_STATUS = '__status';

    /**
     * Get persons by filters with pagination.
     *
     * @param int   $page   Page number
     * @param array $filters Filter criteria
     *
     * @return object Object with objects and count properties
     */
    public static function getByFilters($page, $filters = [])
    {
        $select = (self::getSelect())->addWhere(
            self::createWhere(
                self::PERSON_STATUS,
                (!isset($filters[self::FILTER_DISABLED]) || empty($filters[self::FILTER_DISABLED]))
                    ? [self::STATUS_WAITING, self::STATUS_ENABLE, self::STATUS_BANNED]
                    : [self::STATUS_ENABLE]
            )
        );

        if (isset($filters['__total'])) {
            $select->addWhere(
                (new \Application\Assistance\Select\Block(false))
                    ->setWhere(self::createWhere(self::PERSON_FIRST_NAME, dfTrim($filters['__total']), Where::LIKE))
                    ->setWhere(self::createWhere(self::PERSON_SECOND_NAME, dfTrim($filters['__total']), Where::LIKE))
                    ->setWhere(self::createWhere(self::PERSON_PATRONYMIC, dfTrim($filters['__total']), Where::LIKE))
            );
        } else {
            foreach ([
                         self::FILTER_NAME,
                         self::FILTER_EMAIL,
                         self::FILTER_CUSTOMER,
                         self::FILTER_ROLE,
                         self::FILTER_CITY,
                         self::FILTER_LANGUAGE,
                         self::FILTER_COUNTRY,
                         self::FILTER_PROJECT,
                         self::FILTER_STATUS
                     ] as $field) {
                if (isset($filters[$field]) && (is_array($filters[$field]) || dfTrim($filters[$field]) != '')) {
                    switch ($field) {
                        case self::FILTER_NAME:
                            $select->addWhere(
                                (new \Application\Assistance\Select\Block(false))
                                    ->setWhere(self::createWhere(self::PERSON_FIRST_NAME, dfTrim($filters[$field]), Where::LIKE))
                                    ->setWhere(self::createWhere(self::PERSON_SECOND_NAME, dfTrim($filters[$field]), Where::LIKE))
                                    ->setWhere(self::createWhere(self::PERSON_PATRONYMIC, dfTrim($filters[$field]), Where::LIKE))
                            );
                            break;
                        case self::FILTER_ROLE:
                            if (!empty($filters[$field])) {
                                $roleIds = is_array($filters[$field]) ? $filters[$field] : [$filters[$field]];
                                $select->addWhere(
                                    self::createWhere(
                                        (new JoinField())->setBase(PersonRole::getBase())->setTable(PersonRole::$_table)->setField(PersonRole::ROLE_ID),
                                        PersonRole::ROLE_ID,
                                        null,
                                        Where::NOT_NULL,
                                        [PersonRole::getBase(), PersonRole::$_table],
                                        [
                                            static::createWhere((new JoinField())->setBase(PersonRole::getBase())->setTable(PersonRole::$_table)->setField(PersonRole::PROJECT_ID), $roleIds),
                                            static::createWhere(
                                                (new JoinField())->setBase(PersonRole::getBase())->setTable(PersonRole::$_table)->setField(PersonRole::PERSON_ID),
                                                (new JoinField())->setBase(self::getBase())->setTable(self::$_table)->setField(self::PERSON_ID)
                                            )
                                        ]
                                    )
                                );
                            }
                            break;
                        case self::FILTER_CITY:
                            $select->addWhere(self::createWhere(self::CITY_NAME, $filters[$field], Where::LIKE));
                            break;
                        case self::FILTER_STATUS:
                            $select->addWhere(self::createWhere(self::PERSON_STATUS, $filters[$field]));
                            break;
                        case self::FILTER_PROJECT:
                            $select->addWhere(self::createWhere(self::PROJECT_ID, $filters[$field]));
                            break;
                        case self::FILTER_LANGUAGE:
                            $select->addWhere(self::createWhere(self::LANGUAGE_ID, $filters[$field]));
                            break;
                        case self::FILTER_COUNTRY:
                            $select->addWhere(self::createWhere(self::COUNTRY_ID, $filters[$field]));
                            break;
                    }
                }
            }
        }

        $select->_page = $page;
        $where = $select->_where;
        self::setModelResponse(true);

        if (!$res = self::getByCondition($page)) {
            return (object)[
                self::FILTER_RESPONSE_OBJECTS => false,
                self::FILTER_RESPONSE_COUNT => 0
            ];
        }

        return (object)[
            self::FILTER_RESPONSE_OBJECTS => $res,
            self::FILTER_RESPONSE_COUNT => (self::getSelect(\Application\Assistance\Select::COUNT))->addWhere($where)->result()
        ];
    }

    /**
     * Get active users.
     *
     * @return \Modules\Base\Models\Person[]|false
     */
    public static function getActive()
    {
        return (self::getSelect())
            ->addWhere(self::createWhere(self::PERSON_STATUS, self::getActiveStatuses()))
            ->result();
    }

    /**
     * Get users by role ID.
     *
     * @param int|int[] $roleId Role ID(s)
     *
     * @return \Modules\Base\Models\Person[]|false
     */
    public static function getByRole($roleId)
    {
        return (self::getSelect())
            ->addWhere(
                self::createWhere(
                    (new JoinField())->setBase(PersonRole::getBase())->setTable(PersonRole::$_table)->setField(PersonRole::ROLE_ID),
                    PersonRole::ROLE_ID,
                    null,
                    Where::NOT_NULL,
                    [PersonRole::getBase(), PersonRole::$_table],
                    [
                        static::createWhere((new JoinField())->setBase(PersonRole::getBase())->setTable(PersonRole::$_table)->setField(PersonRole::PROJECT_ID), $roleId),
                        static::createWhere(
                            (new JoinField())->setBase(PersonRole::getBase())->setTable(PersonRole::$_table)->setField(PersonRole::PERSON_ID),
                            (new JoinField())->setBase(self::getBase())->setTable(self::$_table)->setField(self::PERSON_ID)
                        )
                    ]
                )
            )
            ->result();
    }

    /**
     * Get users by rule ID.
     *
     * @param int $ruleId Rule ID
     *
     * @return \Modules\Base\Models\Person[]|false
     */
    public static function getByRule($ruleId)
    {
        return (self::getSelect())
            ->addWhere(
                self::createWhere(
                    (new JoinField())->setBase(PersonRole::getBase())->setTable(PersonRole::$_table)->setField(PersonRole::ROLE_ID),
                    PersonRole::ROLE_ID,
                    null,
                    Where::NOT_NULL,
                    [PersonRule::getBase(), PersonRule::$_table],
                    [
                        static::createWhere((new JoinField())->setBase(PersonRule::getBase())->setTable(PersonRule::$_table)->setField(PersonRule::PROJECT_ID), $ruleId),
                        static::createWhere(
                            (new JoinField())->setBase(PersonRule::getBase())->setTable(PersonRule::$_table)->setField(PersonRule::PERSON_ID),
                            (new JoinField())->setBase(self::getBase())->setTable(self::$_table)->setField(self::PERSON_ID)
                        )
                    ]
                )
            )
            ->result();
    }

    /**
     * Get users by parent ID.
     *
     * @param int|array    $personId  Parent person ID(s)
     * @param int|null     $projectId Optional project filter
     *
     * @return \Modules\Base\Models\Person[]|false
     */
    public static function getByParent($personId, $projectId = null)
    {
        if (null !== $projectId) {
            self::setIgnoreProject(true);
        }

        return (self::getSelect())
            ->addWhere([
                self::createWhere(self::PARENT, $personId),
                null === $projectId ? null : self::createWhere(self::PROJECT_ID, $projectId)
            ])
            ->result();
    }

    /**
     * Get count of users by language.
     *
     * @param int $langId Language ID
     *
     * @return int
     */
    public static function relatedByLanguage($langId)
    {
        self::setModelResponse(false);
        return (self::getSelect(\Application\Assistance\Select::COUNT, true))
            ->addWhere(static::createWhere(self::LANGUAGE_ID, $langId))
            ->setGroups([self::LANGUAGE_ID])
            ->result();
    }

    /**
     * Get count of users by country.
     *
     * @param int|int[] $country Country ID(s)
     *
     * @return array
     */
    public static function relatedByCountry($country)
    {
        self::setModelResponse(false);
        return (self::getSelect(\Application\Assistance\Select::SELECT, true))
            ->setFields([
                self::COUNTRY_ID,
                (new FieldHandler())->setCount()->setAlias(Database::$_countField)
            ])
            ->addWhere(static::createWhere(self::COUNTRY_ID, $country))
            ->setGroups([self::COUNTRY_ID])
            ->result();
    }
}