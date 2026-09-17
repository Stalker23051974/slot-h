<?php

namespace Modules\Base\Models\DbTables;

/**
 * StaticFile database table class.
 *
 * Manages cache-busting hashes for static files (CSS, JS, maps).
 * Provides automatic versioning to prevent browser caching issues
 * when files are updated.
 *
 * Features:
 * - File path to hash mapping
 * - Automatic cache busting via file hashes
 * - Used by the frontend asset loader
 * - Updated during autoloader generation
 *
 * @package   Modules\Base\Models\DbTables
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method static \Modules\Base\Models\StaticFile|array|false getRow($ids = null, $page = false, $order = false, $model = true, $cache = true)
 */
class StaticFile extends \Application\Assistance\DatabaseNormal
{
    /** Primary key field */
    const STATIC_FILE_ID = 'static_file_id';

    /** File path (relative to project root) */
    const STATIC_FILE_PATH = 'static_file_path';

    /** MD5 hash of the file content */
    const STATIC_FILE_HASH = 'static_file_hash';

    /** File modification time */
    const STATIC_FILE_TIME = 'static_file_time';

    /** Table name */
    public static $_table = 'static_files';

    /** Primary key field */
    public static $_index = self::STATIC_FILE_ID;

    /**
     * Field definitions.
     *
     * @var array
     */
    public static $_fields = [
        self::STATIC_FILE_ID => [self::FP_INDEX => true],
        self::STATIC_FILE_PATH => [self::FP_TYPE => self::TYPE_STRING],
        self::STATIC_FILE_HASH => [self::FP_TYPE => self::TYPE_STRING],
        self::STATIC_FILE_TIME => [self::FP_NULL => true],
    ];

    /**
     * Get all static files as path => hash mapping.
     *
     * Overrides parent to return a key-value array instead of models.
     *
     * @param bool|int   $page  Page number (unused)
     * @param bool|array $order Order by (unused)
     * @param bool       $model Whether to return models (unused)
     *
     * @return array Associative array of path => hash
     */
    public static function getAll($page = false, $order = false, $model = true)
    {
        $result = [];

        if ($list = parent::getAll(false, false, false)) {
            dfArrayMap(function ($v) use (&$result) {
                $result[$v[self::STATIC_FILE_PATH]] = $v[self::STATIC_FILE_HASH];
            }, $list);
        }

        return $result;
    }
}