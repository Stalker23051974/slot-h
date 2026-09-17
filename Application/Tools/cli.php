<?php
/**
 * Command-line interface (CLI) entry point for queue and background tasks.
 *
 * This script is the main entry point for all CLI operations in the system.
 * It handles:
 * - Parsing command-line arguments (stage, module, controller, action, request)
 * - Loading the autoloader and configuration
 * - Routing the request to the appropriate CLI controller
 *
 * Usage examples:
 *   php cli.php -stage=myproject.ini -module=Base -controller=Cli -action=demon
 *   php cli.php -stage=myproject.ini -module=Base -controller=Cli -action=mail -request={"id":123}
 *
 * The script is typically called by:
 * - Cron jobs (scheduled tasks)
 * - Queue server (background processing)
 * - Deployment scripts (database migrations)
 * - Maintenance tasks (cache clearing, log rotation)
 *
 * @package   Application\Tools
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

// Set memory limit for large operations (queue processing, image handling, etc.)
ini_set('memory_limit', '1024M');

// ============================================================================
// ARGUMENT PARSING
// ============================================================================

/**
 * Define expected CLI parameters and their validation status.
 *
 * Required parameters:
 * - stage: Project configuration name (e.g., myproject.ini)
 * - module: Module name (e.g., Base, Main)
 * - controller: Controller name (e.g., Cli)
 * - action: Action method name (e.g., demon, mail)
 *
 * Optional parameters:
 * - request: JSON-encoded data payload for the action
 */
$check = array_fill_keys(['stage', 'module', 'controller', 'action', 'request'], true);
$cliStage = null;

/**
 * Parse command-line arguments.
 * Format: -stage=value -module=value -controller=value -action=value -request=value
 */
foreach ($argv as $v) {
    foreach ($check as $i => $c) {
        if (true === $c && preg_match("/^\-" . $i . "\=/", $v)) {
            preg_match("/\=(.*)/", $v, $m);
            $check[$i] = $m[1];
        }
    }
    if (true !== $check['stage']) {
        $cliStage = $check['stage'];
    }
}

// ============================================================================
// BOOTSTRAP
// ============================================================================

/**
 * Load the autoloader and configuration.
 * The autoloader is required before loading config.php because
 * config.php may reference classes that need autoloading.
 */
require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Application' . DIRECTORY_SEPARATOR . 'Autoloader.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'config.php');

/**
 * Initialize the autoloader.
 */
\Config\CC::$_loader = new \Application\Autoloader();

/**
 * Register the custom autoloader if AUTOLOAD_MODE is set to 1.
 * This enables on-demand class loading for better performance.
 */
if (AUTOLOAD_MODE == 1) {
    spl_autoload_register(['\\Config\\CC', '__loader']);
}

// ============================================================================
// VALIDATION AND ROUTING
// ============================================================================

/**
 * Validate that required parameters are provided.
 * If module, controller, or action are missing, abort with usage instructions.
 */
if ($check['module'] == 1 || $check['controller'] == 1 || $check['action'] == 1) {
    \Config\CC::abort("\n\nWrong call.\nExample:\n\n\t"
        . \Config\CC::get('console_php')
        . " cli.php -stage=domen_owner -module=SomeModule -controller=SomeController -action=SomeAction [-request=SomeData]\n\n");
}

/**
 * Instantiate the CLI router, which will handle the request.
 * The router will:
 * 1. Validate the controller exists
 * 2. Create a Request object
 * 3. Execute the requested action
 */
new Application\CliRouter($check);