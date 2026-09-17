<?php

namespace Modules\Free;

use Application\Assistance\Controller\Controller as Cr;

/**
 * CRUD configuration for the Free module.
 *
 * Defines module permissions, controllers, and access rules
 * for public pages. All controllers in this module allow
 * guest access.
 *
 * @package   Modules\Free
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */
class Crud extends \Application\Crud
{
    /** Module permission code for Free module */
    const MODULE_PERMISSION = 20;

    /** Permission code for Person controller */
    const PERSON_PERMISSION = 101;

    /** Permission code for Index controller */
    const INDEX_PERMISSION = 102;

    /** Permission code for Scope controller */
    const SCOPE_PERMISSION = 103;

    /** Permission code for Page controller */
    const PAGE_PERMISSION = 104;

    /** Permission code for About controller */
    const ABOUT_PERMISSION = 105;

    /** Permission code for Docs controller */
    const DOCS_PERMISSION = 106;

    /** Permission code for Contact controller */
    const CONTACT_PERMISSION = 107;
    
    /** Index controller name */
    const INDEX_CONTROLLER = 'Index';

    /** Person controller name */
    const PERSON_CONTROLLER = 'Person';

    /** Scope controller name */
    const SCOPE_CONTROLLER = 'Scope';

    /** Page controller name */
    const PAGE_CONTROLLER = 'Page';

    /** About controller name */
    const ABOUT_CONTROLLER = 'About';

    /** Documentation controller name */
    const DOCS_CONTROLLER = 'Docs';

    /** Contacts controller name */
    const CONTACT_CONTROLLER = 'Contact';

    /**
     * Get localized controller names.
     *
     * @return array Controller permission => localized name
     */
    public static function getNames()
    {
        return [
            self::PERSON_PERMISSION => 'Person',
            self::INDEX_PERMISSION => 'Index',
            self::SCOPE_PERMISSION => 'Scope',
            self::PAGE_PERMISSION => 'Pages',
            self::ABOUT_PERMISSION => 'About',
            self::DOCS_PERMISSION => 'Documents',
            self::CONTACT_PERMISSION => 'Contact'
        ];
    }

    /**
     * Controller ID mapping (for permission system).
     *
     * @var array
     */
    public static $_ids = [
        self::PERSON_CONTROLLER => null,
        self::INDEX_CONTROLLER => null,
        self::SCOPE_CONTROLLER => null,
        self::PAGE_CONTROLLER => null,
        self::ABOUT_CONTROLLER => null,
        self::DOCS_CONTROLLER => null,
        self::CONTACT_CONTROLLER => null,
    ];

    /**
     * List of controllers and their permission codes.
     *
     * @var array
     */
    public static $_controllers = [
        self::PERSON_CONTROLLER => self::PERSON_PERMISSION,
        self::INDEX_CONTROLLER => self::INDEX_PERMISSION,
        self::SCOPE_CONTROLLER => self::SCOPE_PERMISSION,
        self::PAGE_CONTROLLER => self::PAGE_PERMISSION,
        self::ABOUT_CONTROLLER => self::ABOUT_PERMISSION,
        self::DOCS_CONTROLLER => self::DOCS_PERMISSION,
        self::CONTACT_CONTROLLER => self::CONTACT_PERMISSION,
    ];

    /**
     * List of modules and their permissions.
     *
     * @var array
     */
    public static $_modules = [
        \Application\Crud::MODULE_FREE => self::MODULE_PERMISSION
    ];

    /**
     * Access rules for module actions.
     *
     * All actions in the Free module require READ_RULE.
     *
     * @var array
     */
    public static $_rules = [
        self::MODULE_PERMISSION => [
            self::INDEX_PERMISSION => [
                Cr::INDEX_ACTION => Cr::READ_RULE,
                'auth' => Cr::READ_RULE
            ],
            self::PERSON_PERMISSION => [
                'getter' => Cr::READ_RULE,
                'remind' => Cr::READ_RULE,
            ],
            self::SCOPE_PERMISSION => [
                'get' => Cr::READ_RULE,
                'fonts' => Cr::READ_RULE,
            ],
            self::PAGE_PERMISSION => [
                'policy' => Cr::READ_RULE,
                'terms' => Cr::READ_RULE,
            ],
            self::ABOUT_PERMISSION => [
                Cr::INDEX_ACTION => Cr::READ_RULE
            ],
            self::DOCS_PERMISSION => [
                Cr::INDEX_ACTION => Cr::READ_RULE
            ],
            self::CONTACT_PERMISSION => [
                Cr::INDEX_ACTION => Cr::READ_RULE
            ],
        ],
    ];

    /**
     * Controllers with project-specific variants.
     *
     * @return array List of controller names
     */
    public static function getSeparateControllers()
    {
        return [self::INDEX_CONTROLLER];
    }
}