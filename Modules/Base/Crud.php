<?php

/**
 * CRUD configuration for the Base module.
 *
 * Defines module permissions, controllers, and access rules
 * for the Base module. This module handles core system functionality
 * including user management, queues, logging, and authentication.
 *
 * @package   Modules\Base
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Modules\Base;

use \Application\Assistance\Controller\Controller as Cr;

class Crud extends \Application\Crud
{
    /** Module permission code for Base module */
    const MODULE_PERMISSION = 10;

    /** Permission code for CLI controller actions */
    const CLI_PERMISSION = 101;

    /** CLI controller name */
    const CLI_CONTROLLER = 'Cli';

    /**
     * List of controllers and their permission codes.
     *
     * @var array
     */
    public static $_controllers = [
        self::CLI_CONTROLLER => self::CLI_PERMISSION,
    ];

    /**
     * List of modules and their permissions.
     *
     * @var array
     */
    public static $_modules = [
        \Application\Crud::MODULE_BASE => self::MODULE_PERMISSION
    ];

    /**
     * Access rules for module actions.
     *
     * Structure: permission_code => controller => action => rule_level
     *
     * @var array
     */
    public static $_rules = [
        self::MODULE_PERMISSION => [
            self::CLI_PERMISSION => [
                'mail'  => Cr::READ_RULE,
                'image' => Cr::READ_RULE,
                'dump'  => Cr::READ_RULE,
            ],
        ]
    ];
}