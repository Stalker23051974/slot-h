<?php
/**
 * Entry point for all HTTP requests.
 *
 * Handles initial environment setup, debugging, request normalization,
 * and delegates processing to the application loader.
 *
 * @package   Application
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

// Enable error display for development and debugging
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// Set memory limit to accommodate large file uploads and heavy operations
ini_set('memory_limit', '1024M');

// Load global configuration constants
require_once('Config' . DIRECTORY_SEPARATOR . 'config.php');

/**
 * Start XHProf profiling when in developer mode and extension is available.
 * Captures CPU, memory usage, and excludes built-in functions for cleaner traces.
 */
if (true === DEVELOPER_MODE && function_exists('xhprof_enable')) {
    xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY + XHPROF_FLAGS_NO_BUILTINS);
}

/**
 * Normalize the request URI.
 *
 * If 'request' parameter is not explicitly set via GET/POST,
 * extract it from the REQUEST_URI by removing the leading slash.
 * Also remove sensitive session parameters from the global request array.
 */
if (!isset($_REQUEST['request'])) {
    $_REQUEST['request'] = preg_replace("/^\//", '', $_SERVER['REQUEST_URI']);

    // Remove sensitive data that should not be exposed in logs or processing
    unset($_REQUEST['PHPSESSID'], $_REQUEST['user_id']);
}

// Load the core application loader, which initializes routing and handles the request
require_once('Config' . DIRECTORY_SEPARATOR . 'loader.php');

/**
 * Terminate the application execution.
 *
 * The CC (Core Controller) abort method finalizes output buffering,
 * flushes any remaining data, and exits the script cleanly.
 */
\Config\CC::abort();