<?php

/**
 * Custom exception handler for the application.
 *
 * This class provides exception handling with configurable behavior:
 * - Displays detailed exception information in development mode
 * - Logs exceptions for debugging
 * - Aborts execution with a user-friendly message in production
 * - Conditionally shows stack traces when SHOW_TRACE is enabled
 *
 * Note: This class is marked for removal in future versions.
 * It may be deprecated in favor of proper PHP exception handling.
 *
 * @package   Application\Assistance
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 * @deprecated This class is scheduled for removal. Use proper exception handling instead.
 */

namespace Application\Assistance;

class Exception
{
    /**
     * Create a new exception handler instance.
     *
     * This constructor immediately processes the exception by logging it
     * and displaying it according to the current configuration.
     *
     * @param string $message The error message to display
     * @param mixed  $e       The original exception/error object (optional)
     */
    public function __construct($message, $e)
    {
        $this->showException($message, $e);
        \Application\Helpers\DfDebug::dflog([$message, debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)]);
    }

    /**
     * Display the exception based on configuration settings.
     *
     * Behavior:
     * - If $e is not false: var_dump the exception (detailed debug output)
     * - If SHOW_TRACE is true: output a debug backtrace and die()
     * - Otherwise: abort with the error message (clean user-friendly output)
     *
     * This method is protected so it can be overridden in child classes
     * if custom exception display logic is needed.
     *
     * @param string $message The error message
     * @param mixed  $e       The original exception/error object
     */
    protected function showException($message, $e)
    {
        /**
         * Show detailed exception information if available.
         * Typically used in development environments.
         */
        if (false !== $e) {
            var_dump($e);
        }

        /**
         * Show stack trace when SHOW_TRACE is enabled.
         * Useful for debugging complex issues.
         */
        if (true === SHOW_TRACE) {
            echo debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            die();
        }

        /**
         * Gracefully abort with the error message.
         * This is the production behavior - shows a clean error message
         * without exposing internal details.
         */
        \Config\CC::abort($message);
    }
}