<?php

/**
 * Debug and logging helper.
 *
 * This class provides debugging and logging utilities for the application:
 * - File-based logging with timestamps
- Conditional logging based on debug flags
 * - Query logging for database operations
 * - Queue task name mapping
 * - Debug output to browser (when enabled)
 *
 * Logs are stored in the project's Config/Logs directory with
 * date-stamped filenames for easy rotation and analysis.
 *
 * @package   Application\Helpers
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Helpers;

use \Application\Helpers\Queues\Queue as Q;
use \Config\CC as C;

class DfDebug
{
    /**
     * Write a log entry to file.
     *
     * Supports logging of:
     * - Strings, arrays, objects (print_r format)
     * - Booleans (converted to TRUE/FALSE)
     * - Null values (converted to NULL)
     *
     * Log format: YYYY-MM-DD HH:MM:SS    title    value
     *
     * When SHOW_QUERY_FLAG is set, also outputs to browser.
     *
     * @param mixed  $value    Data to log (string, array, object, etc.)
     * @param string $title    Log entry title/context
     * @param string $file     Log file name (without extension)
     * @param bool   $debug    Force logging even if DEBUG is false
     * @param bool   $separate Add date suffix to filename (e.g., system_log_2026-01-15)
     */
    public static function dflog($value, $title = '', $file = 'system_log', $debug = false, $separate = false)
    {
        if (true === $debug || true === DEBUG) {
            /**
             * Add date suffix for daily log rotation.
             */
            if (true === $separate) {
                $file .= '_' . date('Y-m-d');
            }

            /**
             * Format array/object values using print_r.
             */
            if (is_array($value) || is_object($value)) {
                error_log(
                    date('Y-m-d H:i:s') . "\t" . $title . "\t" . print_r($value, true) . "\n\n",
                    '3',
                    DEBUG_PATH . $file
                );

                if (isset($_REQUEST[SHOW_QUERY_FLAG])) {
                    echo "\n-----\n" . $title . "\n" . print_r($value, true) . "\n-----\n";
                }
            } else {
                /**
                 * Format scalar values.
                 */
                if (is_bool($value)) {
                    $value = $value ? 'TRUE' : 'FALSE';
                } else if (is_null($value)) {
                    $value = 'NULL';
                } else {
                    $value = '"' . $value . '"';
                }

                error_log(
                    date('Y-m-d H:i:s') . "\t" . $title . "\t" . $value . "\n",
                    '3',
                    DEBUG_PATH . $file
                ) or die(json_encode([DEBUG_PATH . $file => error_get_last()]));

                if (isset($_REQUEST[SHOW_QUERY_FLAG])) {
                    echo "\n-----\n" . $title . "\n" . print_r($value, true) . "\n-----\n";
                }
            }
        }
    }

    /**
     * Write a debug log entry for query monitoring.
     *
     * Used by the database layer to log SQL queries and execution times.
     * Outputs to either the browser (echo) or a log file depending on
     * the current debug configuration.
     *
     * @param bool|string $debug Debug message or false
     *
     * @return bool|string Microtime for timing calculations, or true if not logging
     */
    public static function writeLog($debug = false)
    {
        if (true === SHOW_QUERIES || isset($_REQUEST[SHOW_QUERY_FLAG]) || true === \Application\Assistance\Database::$_showQuery || null !== C::$_logFile) {
            if (false !== $debug) {
                if (null === C::$_logFile) {
                    echo $debug;
                } else {
                    self::dflog($debug, '', C::$_logFile, true);
                }
            }
            return microtime();
        }
        return true;
    }

    /**
     * Get human-readable names for queue task types.
     *
     * Maps numeric action constants to descriptive names
     * for logging and debugging purposes.
     *
     * @return array Associative array of action => description
     */
    public static function getQueueNames()
    {
        return [
            Q::QUEUE_ACTION_MAIL   => 'Email sending',
            Q::QUEUE_ACTION_IMAGE  => 'Image processing',
            Q::QUEUE_ACTION_DUMP   => 'Dump database'
        ];
    }
}