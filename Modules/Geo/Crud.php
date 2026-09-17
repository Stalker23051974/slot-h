<?php

namespace Modules\Geo;

use \Application\Assistance\Controller\Controller as Controller;
use \Config\CC as C;

/**
 * CRUD configuration for the Geo module.
 *
 * Defines module permissions, controllers, and access rules
 * for geographic data management.
 *
 * @package   Modules\Geo
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */
class Crud extends \Application\Crud
{
    /** Module permission code for Geo module */
    const MODULE_PERMISSION = 30;

    /** Permission code for Language controller */
    const LANGUAGE_PERMISSION = 102;

    /** Language controller name */
    const LANGUAGE_CONTROLLER = 'Language';

    /**
     * Get localized controller names.
     *
     * @return array Controller permission => localized name
     */
    public static function getNames()
    {
        return [
            self::LANGUAGE_PERMISSION => C::locale('Languages'),
        ];
    }

    /**
     * Controller ID mapping (for permission system).
     *
     * @var array
     */
    public static $_ids = [
        self::LANGUAGE_CONTROLLER => 'id',
    ];

    /**
     * List of controllers and their permission codes.
     *
     * @var array
     */
    public static $_controllers = [
        self::LANGUAGE_CONTROLLER => self::LANGUAGE_PERMISSION,
    ];

    /**
     * List of modules and their permissions.
     *
     * @var array
     */
    public static $_modules = [
        \Application\Crud::MODULE_GEO => self::MODULE_PERMISSION
    ];

    /**
     * Access rules for module actions.
     *
     * @var array
     */
    public static $_rules = [
        self::MODULE_PERMISSION => [
            self::LANGUAGE_PERMISSION => [
                Controller::INDEX_ACTION => Controller::READ_RULE,
                Controller::EDIT_ACTION => Controller::READ_RULE,
            ],
        ]
    ];
}