<?php

/**
 * Abstract database engine class.
 *
 * This is the foundation for all database connection implementations.
 * It provides common properties and utilities used by concrete
 * database drivers (MySQL, PostgreSQL, MongoDB, etc.).
 *
 * The engine handles:
 * - Connection pooling (multiple connections per request)
 * - Connection credentials (host, port, user, password)
 * - Table name resolution for SELECT queries
 * - Boolean value normalization across different DBMS
 *
 * Concrete implementations must extend this class and implement
 * the actual connection logic, query execution, and result parsing.
 *
 * @package   Application\Assistance\DbEngine
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Assistance\DbEngine;

abstract class DbEngine
{
    /**
     * Connection pool.
     *
     * Stores active database connections indexed by connection name.
     * Allows multiple database connections within a single request.
     *
     * @var \mysqli[]|mixed[] Array of connection objects
     */
    protected static $connect = [];

    /**
     * Database name.
     *
     * @var string
     */
    public static $_base;

    /**
     * Database server hostname or IP address.
     *
     * @var string
     */
    public static $_host;

    /**
     * Database server port number.
     *
     * @var string|int
     */
    public static $_port;

    /**
     * Database username.
     *
     * @var string
     */
    public static $_user;

    /**
     * Database password.
     *
     * @var string
     */
    public static $_password;

    /**
     * False value representation for the specific DBMS.
     *
     * Different databases represent boolean false differently:
     * - MySQL: 0
     * - PostgreSQL: false
     * - MongoDB: false
     *
     * @var mixed
     */
    public static $_false = null;

    /**
     * True value representation for the specific DBMS.
     *
     * @var mixed
     */
    public static $_true = true;

    /**
     * Get the fully qualified table name for a SELECT query.
     *
     * Returns the table name with appropriate quoting for the DBMS.
     * Format: `database`.`table`
     *
     * @param \Application\Assistance\Select $select SELECT object containing
     *                                               database and table names
     *
     * @return string Quoted table name (database.table)
     */
    public static function getTableName($select)
    {
        return '`' . $select->_base . '`.`' . $select->_from . '`';
    }
}