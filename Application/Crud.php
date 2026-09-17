<?php

/**
 * Base CRUD (Create, Read, Update, Delete) configuration class.
 *
 * This class serves as the foundation for module configuration across
 * the entire application. It defines:
 * - Available modules and their types (admin/shop)
 * - Module folder structure
 * - Controller and action mappings for access control
 *
 * Each module extends this class to define its own controllers,
 * actions, and permission rules. This provides a unified interface
 * for module registration and access validation.
 *
 * @package   Application
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application;

class Crud
{
    // ============================================================================
    // MODULE NAME CONSTANTS
    // ============================================================================

    /** Core module: user management, authentication, queues */
    const MODULE_BASE = 'Base';

    /** Public module: guest-accessible pages (landing, about, etc.) */
    const MODULE_FREE = 'Free';

    /** Geographic data module: countries, cities, states, currencies */
    const MODULE_GEO = 'Geo';

    /**
     * List of enabled modules with their visibility type.
     *
     * Structure: module_name => is_admin_only
     * - true: Module is only visible/accessible in the admin panel
     * - false: Module is visible/accessible in the public (shop) interface
     *
     * @var array
     */
    public static $_modules = [
        Crud::MODULE_BASE => true,  // Core functionality - admin only
        Crud::MODULE_FREE => true,  // Public pages - visible to all
        Crud::MODULE_GEO => true    // Geo data - admin only
    ];

    // ============================================================================
    // FILE SYSTEM CONSTANTS
    // ============================================================================

    /** Root folder name where all modules are stored */
    const MODULE_FOLDER = 'Modules';

    /** Folder name for model classes within a module */
    const MODEL_FOLDER = 'Models';

    /** Folder name for database table classes within a module */
    const DATABASE_FOLDER = 'DbTables';

    // ============================================================================
    // MODULE CONFIGURATION METHODS
    // ============================================================================

    /**
     * Get list of core modules that are always loaded.
     *
     * These modules provide foundational functionality required
     * by all projects regardless of configuration.
     *
     * @return array List of core module names
     */
    public static function getBaseModules()
    {
        return [
            self::MODULE_BASE,
            self::MODULE_GEO
        ];
    }

    /**
     * Get controllers that have project-specific variants.
     *
     * Returns an array of controller names that should be resolved
     * with project-specific implementations (e.g., Controller_Project51).
     *
     * This allows projects to override specific controllers while
     * keeping the same base functionality for others.
     *
     * Override this method in module-specific Crud classes to enable
     * project-specific controller resolution.
     *
     * @return array List of controller names with project variants
     */
    public static function getSeparateControllers()
    {
        return [];
    }

    // ============================================================================
    // PERMISSION RULES
    // ============================================================================

    /**
     * Permission rules for controllers and actions.
     *
     * Structure:
     * [
     *     module_name => [
     *         'permission' => <integer permission code>,
     *         'controllers' => [
     *             controller_name => [
     *                 action_name => <rule_code>,
     *                 ...
     *             ],
     *             ...
     *         ]
     *     ]
     * ]
     *
     * Each action can have a specific rule code that determines:
     * - Access level required (view, edit, delete, etc.)
     * - Whether the action is allowed for the current user role
     *
     * This array is populated by module-specific Crud implementations.
     *
     * @var array|bool False until initialized by child classes
     */
    public static $_rules = false;

    // ============================================================================
    // CONTROLLER REGISTRY
    // ============================================================================

    /**
     * Registry of all controllers across all modules.
     *
     * Structure:
     * [
     *     module_name => [
     *         controller_name => [
     *             action_name => <rule_code>,
     *             ...
     *         ],
     *         ...
     *     ]
     * ]
     *
     * @var array|bool False until built via getControllers()
     */
    public static $_controllers = false;

    /**
     * Build and return the complete controller registry.
     *
     * Collects controller definitions from all enabled modules.
     * Each module's Crud class provides its own $_controllers array.
     *
     * The resulting registry is used for:
     * - Access control validation (can user access this action?)
     * - URL routing and action resolution
     * - Permission rule lookups
     *
     * @return array|bool Complete controller registry or false if not built
     */
    public static function getControllers()
    {
        if (false === self::$_controllers) {
            self::$_controllers = [];

            /**
             * Iterate through all enabled modules.
             * Each module must have a Crud class defining its controllers.
             */
            foreach (static::$_modules as $module => $type) {
                $crud = '\\' . \Application\Crud::MODULE_FOLDER . '\\' . $module . '\Crud';
                /** @var \Application\Crud $crud */
                static::$_controllers[$module] = $crud::$_controllers;
            }
        }

        return self::$_controllers;
    }
}