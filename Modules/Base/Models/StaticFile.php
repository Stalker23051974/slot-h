<?php

namespace Modules\Base\Models;

/**
 * StaticFile model class.
 *
 * Represents a static file hash record. Stores cache-busting hashes
 * for CSS, JS, and map files to prevent browser caching issues.
 *
 * @package   Modules\Base\Models
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method int getStaticFileId()
 * @method $this setStaticFileId(int $static_file_id)
 * @method string getStaticFilePath()
 * @method $this setStaticFilePath(string $static_file_path)
 * @method string getStaticFileHash()
 * @method $this setStaticFileHash(string $static_file_hash)
 * @method int getStaticFileTime()
 * @method $this setStaticFileTime(int $static_file_time)
 */
class StaticFile extends \Application\Assistance\Model
{
    /** @var int Primary key */
    public $static_file_id;

    /** @var string File path (relative to project root) */
    public $static_file_path;

    /** @var string MD5 hash of the file content */
    public $static_file_hash;

    /** @var int File modification time */
    public $static_file_time;
}