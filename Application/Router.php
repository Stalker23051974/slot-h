<?php

/**
 * HTTP request router.
 *
 * This class handles the complete request lifecycle:
 * 1. Session initialization
 * 2. Request parsing and normalization
 * 3. Module/controller/action resolution
 * 4. Project validation and environment setup
 * 5. Controller instantiation and execution
 *
 * This is the main entry point that bridges the HTTP request
 * to the appropriate application logic.
 *
 * @package   Application
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application;

use \Modules\Base\Models\DbTables as db;
use \Config\CC as C;
use \Application\Assistance\Controller\Controller as Cr;

class Router
{

    /**
     * Initialize the router and process the current request.
     *
     * Constructor performs the following steps:
     * - Starts or resumes the user session
     * - Parses the request URI into module/controller/action
     * - Validates the project exists in the database
     * - Configures timezone and upload limits
     * - Defines project-specific constants
     * - Instantiates and executes the target controller
     */
    public function __construct()
    {
        // ====================================================================
        // SESSION INITIALIZATION
        // ====================================================================

        /**
         * Start or resume the user session.
         * Session data includes user authentication, navigation history,
         * and project-specific state.
         */
        session_start();

        /**
         * Initialize the breadcrumb navigation history for the current project.
         * Stores the user's navigation path for "back" functionality.
         */
        if (!isset($_SESSION['crumbs' . CURRENT_PROJECT])) {
            $_SESSION['crumbs' . CURRENT_PROJECT] = [];
        }

        // ====================================================================
        // REQUEST PARSING
        // ====================================================================

        /**
         * Parse the incoming HTTP request.
         * Extracts module, controller, action, and parameters from the URI.
         */
        $request = new Assistance\Request();
        $module = $request->getModule();

        /**
         * Build the bootstrap class name for the requested module.
         * Bootstrap.php contains module metadata, permissions, and initialization logic.
         */
        $bootStrap = '\\' . \Application\Crud::MODULE_FOLDER . '\\' . $module . '\Bootstrap';

        /**
         * Fallback to the default module if the requested module's bootstrap
         * does not exist. This ensures graceful degradation.
         */
        if (!class_exists($bootStrap)) {
            $request->setModule(C::get('default_module'));
            $bootStrap = '\\' . \Application\Crud::MODULE_FOLDER . '\\' . C::get('default_module') . '\Bootstrap';
            $module = C::get('default_module');
        }

        /**
         * Check if the module allows guest (unauthenticated) access.
         * This is defined in the module's Bootstrap::$_allow_guest property.
         */
        /** @var \Application\Bootstrap $bootStrap */
        $request->_allow_guest = $bootStrap::$_allow_guest;

        // ====================================================================
        // CONTROLLER RESOLUTION
        // ====================================================================

        // Set actual controller
        if (!isset($controller)) {
            if (true === $request->_allow_guest) {
                $controller = '\\' . implode('\\', [
                        \Application\Crud::MODULE_FOLDER,
                        $module,
                        'Controllers',
                        $request->getController()
                    ]);

                if (!class_exists($controller)) {
                    $request->setAction(C::get('default_action'));
                    $controller = '\\' . implode('\\', [
                            \Application\Crud::MODULE_FOLDER,
                            C::get('default_module'),
                            'Controllers',
                            $module == DEFAULT_BASE_MODULE ? DEFAULT_BASE_CONTROLLER : C::get('default_controller')
                        ]);
                    $request->setController($module == DEFAULT_BASE_MODULE ? DEFAULT_BASE_CONTROLLER : C::get('default_controller'));
                }
            } else {
                /**
                 * For protected modules: redirect to the login controller.
                 * Unauthenticated users cannot access modules that require authentication.
                 */
                $controller = '\Application\Assistance\Controller\Login';
                $request->setAction('login');
            }
        }

        // ====================================================================
        // PROJECT VALIDATION
        // ====================================================================

        /**
         * Validate that the current project exists in the database.
         * If not found, abort with a localized error message.
         */
        if (!$project = db\Project::getRow(CURRENT_PROJECT)) {
            C::abort('The system cannot find the project');
        }


        /**
         * Set the default timezone for this project.
         * Timezone is stored in the project's timezone relationship.
         */
        /** @var \Modules\Base\Models\Project $project */
        date_default_timezone_set($project->linkTimezoneId()->getTimezoneStandart());

        // ====================================================================
        // UPLOAD LIMIT CALCULATION
        // ====================================================================

        /**
         * Determine the effective maximum upload size.
         * Uses the smaller of upload_max_filesize and post_max_size.
         * Supports G, M, and K suffixes.
         */
        $fSize = [ini_get('upload_max_filesize'), ini_get('post_max_size')];
        foreach ($fSize as &$v) {
            switch (strtolower($v[strlen($v) - 1])) {
                case 'g':
                    $v = $v * 1024 * 1024 * 1024;
                    break;
                case 'm':
                    $v = $v * 1024 * 1024;
                    break;
                case 'k':
                    $v = $v * 1024;
                    break;
            }
        }
        define('MAX_UPLOAD_SIZE', $fSize[$fSize[0] > $fSize[1] ? 1 : 0]);

        // ====================================================================
        // PROJECT-SPECIFIC CONSTANTS
        // ====================================================================

        /**
         * Helper class names for procedures and person data.
         * These are project-specific implementations that can be overridden
         * per project for customization.
         */
        define('PROCEDURE_HELPER', '\Application\Helpers\Procedures\Project_' . $project->_id() . '\\' . C::get('db_helper'));
        define('PERSON_HELPER', '\Application\Helpers\Person\Project' . $project->_id());

        /** Default country ID (fallback value) */
        define('DEFAULT_COUNTRY', 2);

        /** Landing page URL for the project */
        define('LANDING_URL', $project->getProjectLanding());

        /** Project title (reserved for future use) */
        define('PROJECT_TITLE', '');

        /** Current module name stored for session persistence */
        define('CURRENT_MODULE_SESSION', $module);

        /** View engine name (e.g., phtml, twig, etc.) */
        define('VIEW_ENGINE', C::get('view_engine'));

        // ====================================================================
        // CONTROLLER INSTANTIATION
        // ====================================================================

        /**
         * Build the CRUD class name for the current module.
         * Crud.php defines available controllers and actions for the module.
         */
        $crud = '\\' . \Application\Crud::MODULE_FOLDER . '\\' . $request->getModule() . '\Crud';
        /** @var \Application\Crud $crud */

        /**
         * Build the final controller class name.
         * Handles project-specific controller variants when the controller
         * is listed in getSeparateControllers().
         *
         * This allows projects to have custom controller implementations
         * while sharing the same base logic.
         */
        $call = $controller . (dfInArray($request->getController(), $crud::getSeparateControllers()) ? '\\' . $request->getController() . USE_PROJECT : '');

        /**
         * Store the current page in session for navigation/crumb purposes.
         * This prevents the same page from being added multiple times.
         */
        $_SESSION[Cr::PREVENT_PAGE . CURRENT_PROJECT] = [
            $request->getModule(),
            $request->getController(),
            $request->getParams(Cr::PAGE_PARAM)
        ];

        /**
         * Instantiate the controller and execute the request.
         * The controller constructor handles the action dispatch.
         */
        new $call($request, []);
    }
}