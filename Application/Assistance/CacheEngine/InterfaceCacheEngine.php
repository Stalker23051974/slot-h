<?php

/**
 * Cache engine interface.
 *
 * Defines the contract for all cache engine implementations.
 * Any cache backend (Redis, Memcached, APCu, file-based, etc.)
 * must implement these methods to be compatible with the system.
 *
 * The interface ensures consistent behavior across different
 * caching solutions, allowing the system to switch between
 * engines without changing application code.
 *
 * @package   Application\Assistance\CacheEngine
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Assistance\CacheEngine;

interface InterfaceCacheEngine
{
    /**
     * Establish connection to the cache backend.
     *
     * Called during system initialization to set up the
     * underlying cache connection (e.g., Redis connect,
     * Memcached connect, file system initialization).
     *
     * @param string $separator Key separator character (e.g., ':', '_')
     * @param string $allPart   All-part identifier for batch operations
     * @param string $listPart  List-part identifier for list operations
     *
     * @return void
     */
    public function connect($separator, $allPart, $listPart);

    /**
     * Retrieve a value from the cache.
     *
     * @param string $key Full cache key
     *
     * @return array|bool The cached data as array, or false on miss/error
     */
    public function get($key);

    /**
     * Store a value in the cache.
     *
     * @param string $key   Full cache key
     * @param string $value Value to store (should be JSON-encoded string)
     *
     * @return bool True on success, false on failure
     */
    public function set($key, $value);

    /**
     * Delete a value from the cache.
     *
     * @param string $key Full cache key
     *
     * @return bool True on success, false on failure
     */
    public function del($key);
}