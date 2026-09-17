<?php

/**
 * Command-line interface (CLI) router.
 *
 * This class handles requests from the command line, typically invoked
 * via the queue server or maintenance scripts. It translates CLI
 * arguments into a format compatible with the standard request lifecycle.
 *
 * CLI usage pattern:
 * php Application/Tools/cli.php -stage=<project> -module=<module>
 *     -controller=<controller> -action=<action> -request=<json_params>
 *
 * The router mimics the HTTP request flow but without session/HTTP
 * dependencies, allowing controllers to be executed in a headless
 * environment.
 *
 * @package   Application
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application;

class CliRouter
{

    /**
     * Initialize and execute a CLI request.
     *
     * This constructor:
     * 1. Validates that the requested controller exists
     * 2. Creates a Request object populated with CLI parameters
     * 3. Instantiates the controller to handle the request
     *
     * The 'request' parameter, if provided as JSON, is decoded and
     * merged into the request parameters for the controller.
     *
     * @param array $params CLI parameters from argv or getopt:
     *                      - module: Module name (e.g., Base)
     *                      - controller: Controller name (e.g., Cli)
     *                      - action: Action method name (e.g., demon)
     *                      - request: JSON-encoded parameters (optional)
     *                      - stage: Project stage/configuration (handled elsewhere)
     */
    public function __construct($params)
    {
        // ====================================================================
        // CONTROLLER VALIDATION
        // ====================================================================

        /**
         * Build the fully qualified controller class name.
         * Format: \Modules\<module>\Controllers\<controller>
         */
        $controller = '\\' . implode('\\', [
                \Application\Crud::MODULE_FOLDER,
                $params['module'],
                'Controllers',
                $params['controller']
            ]);

        /**
         * Abort with error message if the controller does not exist.
         * This prevents silent failures in cron jobs and queue workers.
         */
        if (!class_exists($controller)) {
            \Config\CC::abort("\n\n" . $params['controller'] . " not exist in module " . $params['module'] . "\n\n");
        }

        // ====================================================================
        // REQUEST PREPARATION
        // ====================================================================

        /**
         * Set the request URI for compatibility with the Request parser.
         * Even in CLI mode, the Request class expects a 'request' parameter
         * to determine the route. This ensures consistent behavior.
         */
        $_REQUEST['request'] = array_key_exists('request', $params) ? $params['request'] : '';

        /**
         * Instantiate the Request object.
         * This will parse the request URI as if it were an HTTP request,
         * but in CLI mode it's populated with the provided parameters.
         */
        $request = new Assistance\Request();

        /**
         * Set the action from CLI parameters.
         * Overrides whatever the Request parser might have determined.
         */
        $request->setAction($params['action']);

        /**
         * If a 'request' parameter was provided as JSON, decode it and
         * populate the request's parameters and POST data.
         *
         * This allows complex data structures to be passed to CLI controllers:
         * -request='{"id":123,"status":"processed"}'
         */
        if ($params['request']) {
            if ($params = dfJsonDecode($params['request'])) {
                $request->_params = $params;
                $request->_post = $params;
            }
        }

        // ====================================================================
        // CONTROLLER EXECUTION
        // ====================================================================

        /**
         * Instantiate the controller with the prepared request.
         * The controller's constructor will handle the action dispatch
         * and execute the requested method.
         */
        new $controller($request);
    }
}