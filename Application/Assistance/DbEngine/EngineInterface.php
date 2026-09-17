<?php

/**
 * Database engine interface.
 *
 * Defines the contract for all database engine implementations.
 * Any database system (MySQL, PostgreSQL, MongoDB, etc.) must implement
 * these methods to be compatible with the system's ORM.
 *
 * The interface provides:
 * - Connection management
 * - Query execution
 * - Result fetching
 * - Schema introspection (describe, fields, indexes)
 * - DDL operations (create, alter, drop)
 * - Safety checks for record deletion
 *
 * This abstraction allows the system to switch between different
 * database backends without changing application code.
 *
 * @package   Application\Assistance\DbEngine
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Assistance\DbEngine;

interface EngineInterface
{
    /**
     * Establish a connection to the database.
     *
     * Creates and returns a database connection object using
     * the provided credentials. The connection is typically
     * stored in the connection pool for reuse.
     *
     * @param string $base     Database name
     * @param string $host     Database server hostname
     * @param int    $port     Database server port
     * @param string $user     Database username
     * @param string $password Database password
     *
     * @return mixed Connection resource/object
     */
    public static function connect($base, $host, $port, $user, $password);

    /**
     * Execute a SELECT query.
     *
     * Takes a Select object and executes it against the database.
     * The Select object contains all query components (fields, tables,
     * conditions, joins, order, limit, etc.).
     *
     * @param \Application\Assistance\Select $select Complete SELECT query object
     *
     * @return mixed Query result resource/object
     */
    public static function select($select);

    /**
     * Fetch a row from a query result.
     *
     * Extracts a single row from the result set and returns it
     * as an associative array.
     *
     * @param mixed $result Query result resource/object
     *
     * @return array|false Associative array of row data, or false if no more rows
     */
    public static function resultFetch($result);
}