<?php

/**
 * MySQL database engine implementation using mysqli extension.
 *
 * This class provides the concrete MySQL database connection and query
 * execution using the mysqli (MySQL Improved) extension.
 *
 * Key features:
 * - Connection pooling (reuses connections for the same database)
 * - UTF-8 mb4 support for full Unicode (emojis, etc.)
 * - Extended GROUP_CONCAT limit for large result sets
 * - Disables ONLY_FULL_GROUP_BY for compatibility with legacy queries
 * - Query logging with performance timings
 * - Automatic insert ID retrieval for REPLACE/INSERT operations
 *
 * This implementation extends MysqlAbstract and implements the
 * EngineInterface for complete database functionality.
 *
 * @package   Application\Assistance\DbEngine
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Assistance\DbEngine;

use \Application\Assistance\Select as selectObj;
use \Application\Assistance\Database as db;
use \Config\CC as C;

class Mysql_Mysqli extends MysqlAbstract implements EngineInterface
{
    /**
     * Establish a connection to the MySQL database using mysqli.
     *
     * Creates a new mysqli connection or returns an existing one
     * from the connection pool. Configures connection settings:
     * - UTF-8 mb4 character set (supports full Unicode)
     * - Increased GROUP_CONCAT limit for larger result sets
     * - Disables ONLY_FULL_GROUP_BY for legacy query compatibility
     *
     * @param string $base     Database name
     * @param string $host     Database server hostname (default: false)
     * @param int    $port     Database server port (default: false)
     * @param string $user     Database username (default: false)
     * @param string $password Database password (default: false)
     *
     * @return \mysqli Active mysqli connection object
     *
     * @throws \PDOException On connection failure
     */
    public static function connect($base, $host = false, $port = false, $user = false, $password = false)
    {
        /**
         * Check if connection already exists in the pool.
         * Reuse existing connection to avoid overhead.
         */
        if (!isset(self::$connect[$base]) || is_null(self::$connect[$base])) {
            try {
                $startQuery = \Application\Helpers\DfDebug::writeLog();
                db::$_showQuery = false;

                /**
                 * Create new mysqli connection.
                 * Parameters: host, user, password, database, port
                 */
                self::$connect[$base] = new \mysqli($host, $user, $password, $base, $port);

                /**
                 * Configure connection settings for optimal behavior.
                 */
                // Set UTF-8 with full Unicode support (emojis, special characters)
                self::$connect[$base]->query('SET NAMES utf8mb4;');

                // Increase GROUP_CONCAT limit for large translated text fields
                self::$connect[$base]->query('SET SESSION group_concat_max_len = 1000000;');

                // Disable ONLY_FULL_GROUP_BY for compatibility with legacy queries
                // This allows SELECT with non-aggregated columns in GROUP BY
                self::$connect[$base]->query("SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

                /**
                 * Log connection time for performance monitoring.
                 */
                \Application\Helpers\DfDebug::writeLog(
                    D_EOL . \Application\Helpers\Line::getMicrotimeDelta($startQuery, microtime())
                    . ' | Connect' . D_EOL . '------' . D_EOL
                );
            } catch (\PDOException $e) {
                throw new \PDOException($e);
            }
        }
        return self::$connect[$base];
    }

    /**
     * Execute a SELECT query or arbitrary SQL statement.
     *
     * This method handles all database operations:
     * - SELECT queries return result sets as arrays
     * - REPLACE/INSERT return the auto-generated ID
     * - DELETE/UPDATE return result status
     *
     * Query execution includes:
     * - Connection pooling and reuse
     * - Performance logging with timing
     * - Error logging with backtrace on failure
     * - Automatic result set conversion to associative arrays
     *
     * @param \Application\Assistance\Select|string $select Select object or raw SQL query
     *
     * @return array|int|bool Query results, insert ID, or false on failure
     *
     * @throws \Exception On query execution error
     */
    public static function select($select)
    {
        /**
         * Set database name if not already specified.
         * Uses the main database from configuration as fallback.
         */
        if (empty($select->_base)) {
            $select->_base = C::get(C::get()->main_database)->db_name;
        }

        /**
         * Assemble SQL query from Select object or use raw query.
         */
        $query = $select instanceof selectObj ? static::assemble($select) : $select;

        /**
         * Start timing for performance logging.
         */
        $startQuery = \Application\Helpers\DfDebug::writeLog();

        /**
         * Get connection from pool or create new one.
         */
        $connector = C::get('connector_' . $select->_base);
        $db = !isset(self::$connect[$select->_base]) || is_null(self::$connect[$select->_base])
            ? static::connect(
                $select->_base,
                $connector->db_host,
                $connector->db_port,
                $connector->db_login,
                $connector->db_password
            )
            : self::$connect[$select->_base];

        try {
            /**
             * Log the query if debugging is enabled.
             */
            if (true === SHOW_QUERIES || isset($_REQUEST[SHOW_QUERY_FLAG]) || true === db::$_showQuery || null !== C::$_logFile) {
                /** @var string $startQuery */
                \Application\Helpers\DfDebug::writeLog(
                    D_EOL . \Application\Helpers\Line::getMicrotimeDelta($startQuery, microtime())
                    . ' | QUERY:' . D_EOL . $query . D_EOL
                    . mysqli_error($db) . D_EOL . '------' . D_EOL
                );
            }

            /**
             * Execute the query.
             */
            $result = $db->query($query);

            /**
             * If debugging is enabled and there was an error, output backtrace.
             */
            if (true === SHOW_QUERIES || isset($_REQUEST[SHOW_QUERY_FLAG]) || true === db::$_showQuery || null !== C::$_logFile) {
                if (!empty(mysqli_error($db))) {
                    debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
                    die(D_EOL . '-------------');
                }
            }
        } catch (\Exception $e) {
            throw new \Exception($e);
        }

        /**
         * Reset the query display flag.
         */
        db::$_showQuery = false;

        /**
         * Handle REPLACE/INSERT: return the auto-generated ID.
         */
        if ($select->_type == selectObj::REPLACE) {
            if (!is_bool($result)) {
                $result->fetch_assoc();
            }
            return $db->insert_id;
        }

        /**
         * For DELETE queries, no result set is expected.
         */
        if ($select->_type != selectObj::DELETE) {
            $result = self::resultFetch($result);
        }

        /**
         * Handle boolean results (e.g., successful DELETE).
         */
        if (is_bool($result)) {
            return false;
        }

        /**
         * Clean up and return results.
         */
        $db = null;
        return empty($result) ? false : $result;
    }

    /**
     * Fetch all rows from a result set as an associative array.
     *
     * Uses mysqli_result::fetch_all with MYSQLI_ASSOC to return
     * all rows at once. Returns false if no results.
     *
     * @param \mysqli_result $result MySQLi result object
     *
     * @return array|false All rows as associative array, or false on failure
     */
    public static function resultFetch($result)
    {
        return !is_null($result) && !is_bool($result)
            ? $result->fetch_all(MYSQLI_ASSOC)
            : false;
    }
}