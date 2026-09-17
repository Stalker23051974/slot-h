<?php

namespace Modules\Base\Models\DbTables;

use \Config\CC as C;

/**
 * Project database table class.
 *
 * Manages project/tenant records in the multi-tenant system.
 * Each project represents a separate website or application instance
 * sharing the same codebase but with isolated data.
 *
 * Features:
 * - Multi-tenant project isolation
 * - Project metadata storage
 * - Timezone configuration
 * - Company/business details
 * - Landing page configuration
 * - Project data (JSON) for flexible settings
 *
 * @package   Modules\Base\Models\DbTables
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method static \Modules\Base\Models\Project|array|false getRow($ids = null, $page = false, $order = false, $model = true, $cache = true)
 * @method static \Modules\Base\Models\Project[]|array|false getAll($page = false, $order = false, $model = true)
 */
class Project extends \Application\Assistance\DatabaseExtend
{
    /** Primary key field */
    const PROJECT_ID = 'project_id';

    /** Project name */
    const PROJECT_NAME = 'project_name';

    /** Landing page URL */
    const PROJECT_LANDING = 'project_landing';

    /** Additional project data (JSON) */
    const PROJECT_DATA = 'project_data';

    /** Timezone ID (FK to Timezone table) */
    const TIMEZONE_ID = 'timezone_id';

    /** Session name for project storage */
    const SESSION_NAME = 'project';

    /** Table name */
    public static $_table = 'projects';

    /** Primary key field */
    public static $_index = self::PROJECT_ID;

    /** Name field for getByName() lookups */
    public static $_name = self::PROJECT_NAME;

    /**
     * List template for projects.
     *
     * @var array
     */
    protected static $_listTemplate = [
        self::LIST_TEMPLATE_BASE => ['id' => self::PROJECT_ID, 'value' => self::PROJECT_NAME]
    ];

    /**
     * Field definitions.
     *
     * @var array
     */
    public static $_fields = [
        self::PROJECT_ID => [self::FP_INDEX => true],
        self::PROJECT_NAME => [self::FP_TYPE => self::TYPE_STRING],
        self::PROJECT_LANDING => [self::FP_TYPE => self::TYPE_STRING],
        self::PROJECT_DATA => [self::FP_TYPE => self::TYPE_TEXT],
        self::TIMEZONE_ID => [self::FP_LINK => \Modules\Geo\Models\DbTables\Timezone::class],
    ];

    // ============================================================================
    // PROJECT DATA FIELD KEYS
    // ============================================================================

    /** Bank name */
    const DATA_BANK = 'bank';

    /** EORI number */
    const DATA_EORI = 'eori';

    /** Bank account number */
    const DATA_ACCOUNT = 'account';

    /** Business address */
    const DATA_ADDRESS = 'address';

    /** Company name */
    const DATA_NAME = 'name';

    /** Phone number */
    const DATA_PHONE = 'phone';

    /** Fax number */
    const DATA_FAX = 'fax';

    /** Email address */
    const DATA_EMAIL = 'email';

    /** Tax identification */
    const DATA_TAX = 'tax';

    /**
     * Get localized project data field descriptions.
     *
     * @return array Field key => localized description
     */
    public static function getDataList()
    {
        return [
            self::DATA_NAME => C::locale('Name'),
            self::DATA_ADDRESS => C::locale('Business address'),
            self::DATA_PHONE => C::locale('Phone'),
            self::DATA_FAX => C::locale('Fax'),
            self::DATA_EMAIL => C::locale('Email'),
            self::DATA_BANK => C::locale('Bank name'),
            self::DATA_EORI => C::locale('EORI number'),
            self::DATA_ACCOUNT => C::locale('Checking account'),
            self::DATA_TAX => C::locale('Tax'),
        ];
    }

    /**
     * Get SELECT query with project filtering.
     *
     * Automatically filters by CURRENT_PROJECT unless $_baseQuery is true.
     *
     * @param int          $type            Query type
     * @param bool         $ignoreTranslate Whether to skip translation
     * @param string|bool  $class           Override class name
     *
     * @return \Application\Assistance\Select Configured Select object
     */
    public static function getSelect($type = \Application\Assistance\Select::SELECT, $ignoreTranslate = false, $class = false)
    {
        $select = parent::getSelect($type, $ignoreTranslate, self::class);

        if (false === self::$_baseQuery) {
            $select->addWhere(static::createWhere(self::PROJECT_ID, CURRENT_PROJECT));
        } else {
            self::$_baseQuery = false;
        }

        return $select;
    }
}