<?php

/**
 * Command-line interface (CLI) controller base class.
 *
 * This is the foundation for all controllers that run in CLI mode,
 * typically invoked by the queue server or cron jobs.
 *
 * Unlike standard HTTP controllers, CLI controllers:
 * - Do not use sessions or cookies
 * - Do not render views
 * - Do not perform permission checks (they run with system privileges)
 * - Directly execute the requested action without view rendering
 * - Are used for background tasks, queues, maintenance, and cron jobs
 *
 * The action methods in CLI controllers typically:
 * - Process queue items
 * - Send emails
 * - Generate reports
 * - Perform data imports/exports
 * - Run scheduled maintenance tasks
 *
 * @package   Application\Assistance\Controller
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Assistance\Controller;

class CliController
{
    use \Application\Assistance\Controller\TraitClass;

    /**
     * Request object containing CLI parameters.
     *
     * Populated with:
     * - module: Module name (e.g., Base)
     * - controller: Controller name (e.g., Cli)
     * - action: Action method name (e.g., demon)
     * - request: JSON-encoded parameters (optional)
     *
     * @var \Application\Assistance\Request
     */
    public $_request;

    /**
     * Initialize the CLI controller and execute the requested action.
     *
     * The constructor:
     * 1. Stores the request object
     * 2. Calls the action method specified in the request
     *
     * There is no view rendering or session handling in CLI mode.
     * All output should be directed to stdout/error logs.
     *
     * Example usage from command line:
     * php Application/Tools/cli.php -stage=myproject.ini -module=Base
     *     -controller=Cli -action=demon
     *
     * @param \Application\Assistance\Request $request Request object
     *        containing module, controller, action, and parameters
     */
    public function __construct($request)
    {
        $this->_request = $request;

        /**
         * Execute the requested action method.
         * The action name is suffixed with 'Action' per system convention.
         */
        $this->{$request->getAction() . \Application\Assistance\Controller\Controller::ACTION_SUFFIX}();
    }
}