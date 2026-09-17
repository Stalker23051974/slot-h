<?php

/**
 * Caching database layer.
 *
 * This class extends the base Database class to add caching functionality
 * using external cache engines (Redis, Memcached, etc.).
 *
 * The caching strategy:
 * - Individual records are cached with key: table:ID
 * - All records are cached with key: table:all
 * - List results are cached with key: table:list:template
 * - Cache is invalidated on write operations (save, remove)
 *
 * Cache keys follow the pattern: <table><separator><identifier>
 *
 * This layer sits between the ORM and the actual database, intercepting
 * read operations and serving from cache when possible.
 *
 * @package   Application\Assistance
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Assistance;

use \Config\CC as C;

class DatabaseCache extends Database
{
    /** @var \Application\Assistance\CacheEngine\InterfaceCacheEngine|null Cache engine instance */
    public static $_cache = null;

    /** @var string Separator between table name and identifier */
    public static $_keySeparator = ':';

    /** @var string Separator between field and value in composite keys */
    public static $_valueSeparator = '_';

    /** @var string Separator between multiple segments */
    public static $_segmentSeparator = '|';

    /** @var string Key part for "all records" cache */
    public static $_allPart = 'all';

    /** @var string Key part for "list" cache */
    public static $_listPart = 'list';

    /**
     * Initialize the cache router/engine.
     *
     * Checks if caching is enabled and establishes connection
     * to the cache backend.
     *
     * @return bool True if caching is available and connected
     */
    protected static function router()
    {
        static::$_class = get_called_class();

        // Check if caching is enabled in configuration
        if (true !== C::get('cache_enable') || false === static::$_cache) {
            return false;
        }

        // Lazy-load the cache engine
        if (is_null(static::$_cache)) {
            /** @var \Application\Assistance\CacheEngine\InterfaceCacheEngine $cache */
            $cache = '\Application\Assistance\CacheEngine\\' . C::get('cache_engine');
            static::$_cache = new $cache();
            static::$_cache->connect(self::$_keySeparator, self::$_allPart, self::$_listPart);
        }

        return true;
    }

    /**
     * Get one or more rows with caching.
     *
     * Overrides parent to add cache lookup before database query.
     *
     * Strategy:
     * 1. Check if caching is available and applicable
     * 2. Attempt to fetch from cache
     * 3. For missing IDs, query the database
     * 4. Store missing results in cache for future requests
     *
     * @param int|array|null $ids   Primary key(s)
     * @param bool|int       $page  Page number
     * @param bool|array     $order Order by
     * @param bool           $model Return Model objects
     * @param bool           $cache Use cache
     *
     * @return array|bool|mixed Results
     */
    public static function getRow($ids = null, $page = false, $order = false, $model = true, $cache = true)
    {
        if (false === $ids) {
            return false;
        }

        \Application\Helpers\DfDebug::writeLog(
            'getRow ' . static::$_table . ' ' . (is_array($ids) ? implode(',', $ids) : $ids) . ' '
        );

        /**
         * Bypass cache if:
         * - Cache is disabled
         * - No IDs specified (would fetch all)
         * - Pagination is requested
         * - Custom ordering is requested
         * - Cache router is unavailable
         */
        if (false === $cache || is_null($ids) || false !== $page || false !== $order || false === static::router()) {
            \Application\Helpers\DfDebug::writeLog('return parent::getRow');
            return parent::getRow($ids, $page, $order, $model);
        }

        /** @var \Application\Assistance\CacheEngine\CacheAbstract $cache */
        $cache = static::$_cache;

        /**
         * Try to fetch from cache first.
         * Cache key: table:id
         */
        $result = $cache->getRow(static::$_table, $ids);

        /**
         * Track which IDs were not found in cache.
         */
        $forDb = array_fill_keys(is_array($ids) ? $ids : [$ids], true);
        foreach ($result as $id => $row) {
            if (!is_null($row) && !is_bool($row)) {
                unset($forDb[$id]);
            }
        }

        /**
         * Query database for missing IDs and cache the results.
         */
        if (!empty($forDb)) {
            $forDb = dfArrayKeys($forDb);
            \Application\Helpers\DfDebug::writeLog('parent::getRow: ' . implode(',', $forDb));

            if ($dbGet = parent::getRow($forDb, false, false, $model)) {
                foreach ($dbGet as $id => $get) {
                    $result[$id] = $get;
                    $cache->save(static::$_table . self::$_keySeparator . $id, $get);
                }
            }
        }

        /**
         * Process final results and return.
         */
        $result = self::finalSelect($result);
        return empty($result) ? false : (is_array($ids) ? $result : array_pop($result));
    }

    /**
     * Get all records with caching.
     *
     * Caches the complete result set under a single key.
     *
     * @param bool|false $page  Page number
     * @param bool|false $order Order by
     * @param bool       $model Return Model objects
     *
     * @return array|bool|false|object Results
     */
    public static function getAll($page = false, $order = false, $model = true)
    {
        // Bypass cache if unavailable
        if (false === static::router()) {
            return parent::getAll($page, $order, $model);
        }

        if (false !== $order && !is_array($order)) {
            $order = [$order];
        }

        $key = static::$_table . ':' . static::$_allPart;
        /** @var \Application\Assistance\CacheEngine\CacheAbstract $cache */
        $cache = static::$_cache;

        /**
         * Try to fetch from cache.
         * If not found, query database and cache results.
         */
        if (!$result = $cache->getByKey($key)) {
            if ($result = parent::getAll($page, $order, false)) {
                $cache->save($key, $result);

                // Also cache each individual record
                if (dfCount($result) > 0) {
                    foreach ($result as $k => $v) {
                        $cache->save(static::$_table . self::$_keySeparator . $k, $v);
                    }
                }
            } else {
                return $cache->save($key, true);
            }
        }

        return self::finalSelect($result);
    }

    /**
     * Remove a record from cache and database.
     *
     * Invalidates the cache entry when a record is deleted.
     *
     * @param mixed $id Primary key of record to delete
     *
     * @return array|bool Deletion result
     */
    public static function remove($id)
    {
        // Remove from cache if available
        if (false !== static::router()) {
            /** @var \Application\Assistance\CacheEngine\CacheAbstract $cache */
            $cache = static::$_cache;
            $cache->remove(static::$_table . ':' . $id);
        }

        return parent::remove($id);
    }

    /**
     * Get a cached list result.
     *
     * @param string $module   Module name
     * @param string $entity   Entity/DbTable class name
     * @param string $template Template identifier
     * @param mixed  $ids      Record IDs
     *
     * @return bool|mixed Cached list or false
     */
    public static function getList($module, $entity, $template, $ids)
    {
        if (false === static::router() || empty($ids)) {
            return false;
        }

        /** @var \Application\Assistance\Database $db */
        $db = '\\' . \Application\Crud::MODULE_FOLDER . '\\' . $module
            . '\\' . \Application\Crud::MODEL_FOLDER . '\\'
            . \Application\Crud::DATABASE_FOLDER . '\\' . $entity;

        /** @var \Application\Assistance\CacheEngine\CacheAbstract $cache */
        $cache = static::$_cache;

        /**
         * Cache key: table:list:template
         */
        if ($response = $cache->getByKey(implode(static::$_keySeparator, [
            $db::$_table,
            static::$_listPart,
            $template
        ]))) {
            return $response;
        }

        return false;
    }

    /**
     * Store a value in the cache.
     *
     * @param string $key   Cache key
     * @param mixed  $value Value to cache
     */
    public static function setCacheValue($key, $value)
    {
        if (false !== static::router()) {
            /** @var \Application\Assistance\CacheEngine\CacheAbstract $cache */
            $cache = static::$_cache;
            $cache->save($key, $value);
        }
    }

    /**
     * Retrieve a value from cache by key.
     *
     * @param string $key Cache key
     *
     * @return array|bool Decoded result or false
     */
    public static function getByKey($key)
    {
        if (false !== static::router()) {
            /** @var \Application\Assistance\CacheEngine\CacheAbstract $cache */
            $cache = static::$_cache;
            return self::finalSelect($cache->getByKey($key));
        }
        return false;
    }

    /**
     * Get rows by condition with cache awareness.
     *
     * Caches based on the WHERE conditions when all fields are cacheable.
     *
     * @param bool|int   $page   Page number
     * @param bool|array $order  Order by
     * @param bool       $cache  Use cache
     * @param bool|int   $offset Row limit
     * @param bool       $pop    Return single row
     *
     * @return array|bool|mixed Results
     */
    public static function getByCondition($page = false, $order = false, $cache = true, $offset = false, $pop = false)
    {
        if (false === static::router()) {
            return parent::getByCondition($page, $order, $cache, $offset, $pop);
        }

        /** @var \Application\Assistance\Select\Where[] $where */
        $where = static::$_select->_where;
        $use = [];
        $check = true;

        /**
         * Build a cache key from WHERE conditions.
         * Only cache if all fields are cacheable.
         */
        if (!empty($where)) {
            foreach ($where as $cond) {
                if (false === static::$_fields[is_array($cond->_field) ? implode('.', $cond->_field) : static::$_table . '.' . $cond->_field][self::FP_CACHE]) {
                    $check = false;
                    break;
                }
                $use[] = (is_array($cond->_field) ? implode('.', $cond->_field) : static::$_table . '.' . $cond->_field) . self::$_keySeparator
                    . $cond->_value . self::$_valueSeparator
                    . $cond->_condition;
            }
        }

        /**
         * If conditions are not cacheable, fall back to parent.
         */
        if (false === $check || empty($use)) {
            return parent::getByCondition($page, $order, $cache, $offset, $pop);
        }

        /**
         * Use cached row lookup with the composite key.
         */
        return self::getRow(
            implode(self::$_segmentSeparator, $use),
            $page,
            $order,
            false
        );
    }
}