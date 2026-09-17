<?php
// Autoloader: disable

/**
 * Autoloader generator script.
 *
 * Demo version limit: at this version, use only MySQL !
 *
 * This script scans the entire codebase and generates the Autoloader.php
 * file with class maps and static file hashes. It must be run whenever:
 * - A new class is created
 * - An existing class is moved or renamed
 * - A class is deleted
 * - Static files (CSS/JS) are added or modified
 *
 * The generated autoloader provides:
 * - Class-to-file mapping for on-demand loading
 * - Static file versioning (cache busting via file hashes)
 * - Database migration execution
 *
 * This script is typically run via CLI after code changes:
 *   php Application/Tools/createAutoloader.php
 *
 * @package   Application\Tools
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application;

class Autoloader
{
    /** @var array Class-to-file path mapping */
    public    $_classes    = [];

    /** @var array Class inheritance mapping (child => parent) */
    public    $_map        = [];

    /** @var array Load status tracking for classes */
    protected $_load       = [];

    /** @var array Directories to exclude from scanning */
    protected $_exceptions = [];

    /** @var array Resulting require statements for autoloader */
    protected $_result     = [];

    /** @var string Base directory for path resolution */
    protected $_baseDir;

    /** @var array Static file hashes for cache busting */
    protected $_static     = [];

    /** @var string Path to configuration directory */
    protected $_configPath = '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR;

    /**
     * Initialize and generate the autoloader.
     *
     * The process:
     * 1. Load configuration and exceptions
     * 2. Scan all directories for PHP classes and static files
     * 3. Build class inheritance map
     * 4. Generate require statements
     * 5. Write Autoloader.php file
     * 6. Connect to databases and apply migrations
     * 7. Update static file hashes in database
     */
    public function __construct()
    {
        // Load configuration
        require_once($this->_configPath . 'config.php');
        $this->_exceptions = \Config\CC::getLoadExceptions();

        $dir = dfTrim($_SERVER['DOCUMENT_ROOT']) == '' ? ROOT_PATH : $_SERVER['DOCUMENT_ROOT'];
        $this->_baseDir = $dir;

        // Scan directories
        $this->read($dir);

        // Build class loading order
        $loadList = $this->_load;
        foreach ($loadList as $class => $v) {
            $list = [];
            if (false === $v) {
                $list[] = $this->_classes[$class];
                $this->_load[$class] = true;
            }

            if (isset($this->_map[$class])) {
                $check = $this->_map[$class];
                $counter = 0;
                while (true) {
                    if (isset($this->_load[$check]) && false === $this->_load[$check]) {
                        $list[] = $this->_classes[$check];
                        if (isset($this->_map[$check])) {
                            $this->_load[$this->_map[$check]] = true;
                        }
                    }
                    if (isset($this->_map[$check]) && isset($this->_map[$this->_map[$check]])) {
                        $check = $this->_map[$this->_map[$check]];
                    } else {
                        break;
                    }
                    if ($counter++ > 1000) {
                        die('!');
                    }
                }
            }

            // For AUTOLOAD_MODE 0: generate all require statements
            if (AUTOLOAD_MODE === 0) {
                $szol = dfCount($list) - 1;
                for ($i = $szol; $i >= 0; $i--) {
                    $this->_result[] = "require_once(__DIR__.DIRECTORY_SEPARATOR.'.." . $list[$i] . "');";
                }
            }
        }

        // Write Autoloader.php file
        file_put_contents(
            __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Autoloader.php',
            '<?php namespace Application;class Autoloader{public $_map=null;public $_classes=null;public function __construct(){'
            . implode('', $this->_result)
            . (AUTOLOAD_MODE === 1
                ? '$this->_map=' . preg_replace([
                    "/\:/",
                    "/\{/",
                    "/\}/",
                ], [
                    '=>',
                    '[',
                    ']'
                ], dfJsonEncode($this->_map))
                . ';$this->_classes='
                . '[' . implode(',', dfArrayValues($this->_classes)) . ']' . ';'
                : '')
            . '}} ?>'
        );

        // ====================================================================
        // DATABASE MIGRATIONS AND STATIC FILE SYNC
        // ====================================================================

        if ($files = scandir($this->_configPath . 'Domens' . DIRECTORY_SEPARATOR)) {
            // Load required core classes
            require_once(__DIR__ . '/../Assistance/Select.php');
            require_once(__DIR__ . '/../Assistance/Select/Where.php');
            require_once(__DIR__ . '/../Assistance/Database.php');
            require_once(__DIR__ . '/../Assistance/DbEngine/DbEngine.php');
            require_once(__DIR__ . '/../Assistance/DbEngine/EngineInterface.php');
            require_once(__DIR__ . '/../Assistance/DbEngine/MysqlAbstract.php');
            require_once(__DIR__ . '/../Helpers/DfDebug.php');
            require_once(__DIR__ . '/../Helpers/Line.php');

            $base = [];
            $migrations = false;

            foreach ($files as $entity) {
                if (preg_match("/\.ini$/", $entity)) {
                    $iniFull = parse_ini_file($this->_configPath . 'Domens' . DIRECTORY_SEPARATOR . $entity, true);

                    if (isset($iniFull['main_database']) && isset($iniFull[$iniFull['main_database']])) {
                        $ini = $iniFull[$iniFull['main_database']];

                        // Generate unique key for database connection
                        $key = md5(dfJsonEncode([
                            $ini['db_name'],
                            $ini['db_host'],
                            $ini['db_port'],
                            $ini['db_login'],
                            $ini['db_password']
                        ]));

                        if (!isset($base[$key])) {
                            $base[$key] = true;

                            // Load database engine
                            $class = '\Application\Assistance\DbEngine\\' . $ini['db_engine'];
                            require_once(__DIR__ . '/../Assistance/DbEngine/' . $ini['db_engine'] . '.php');
                            /** @var \Application\Assistance\DbEngine\EngineInterface $class */
                            $connect = $class::connect(
                                $ini['db_name'],
                                $ini['db_host'],
                                $ini['db_port'],
                                $ini['db_login'],
                                $ini['db_password']
                            );
                            /** @var \mysqli $connect */

                            // Sync static file hashes
                            $list = [];
                            if ($res = $connect->query('SELECT * FROM ' . $ini['db_name'] . '.static_files;')) {
                                while ($row = $res->fetch_assoc()) {
                                    if (isset($this->_static[$row['static_file_path']])) {
                                        if ($row['static_file_hash'] != $this->_static[$row['static_file_path']]) {
                                            $connect->query(
                                                'UPDATE ' . $ini['db_name'] . '.static_files SET static_file_hash="'
                                                . $this->_static[$row['static_file_path']]
                                                . '" WHERE static_file_id=' . $row['static_file_id'] . ';'
                                            );
                                        }
                                        unset($this->_static[$row['static_file_path']]);
                                    } else {
                                        $list[] = $row['static_file_id'];
                                    }
                                }

                                // Insert new static files
                                if (dfCount($this->_static) > 0) {
                                    $insert = [];
                                    foreach ($this->_static as $k => $v) {
                                        $insert[] = '(NULL,"' . $k . '","' . $v . '",0)';
                                        if (dfCount($insert) > 900) {
                                            $connect->query(
                                                'INSERT INTO ' . $ini['db_name'] . '.static_files VALUES '
                                                . implode(',', $insert) . ';'
                                            ) or die('!!! 1');
                                            $insert = [];
                                        }
                                    }
                                    if (dfCount($insert) > 0) {
                                        $connect->query(
                                            'INSERT INTO ' . $ini['db_name'] . '.static_files VALUES '
                                            . implode(',', $insert) . ';'
                                        ) or die('!!! 2' . "\n\n" . 'INSERT INTO ' . $ini['db_name']
                                            . '.static_files VALUES ' . implode(',', $insert) . ';' . "\n\n");
                                    }
                                }

                                // Delete orphaned static file records
                                if (dfCount($list) > 0) {
                                    $connect->query(
                                        'DELETE FROM ' . $ini['db_name'] . '.static_files WHERE static_file_id IN ('
                                        . implode(',', $list) . ');'
                                    ) or die('!!! 3');
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Recursively scan a directory for classes and static files.
     *
     * @param string $dir Directory path to scan
     */
    private function read($dir)
    {
        if ($files = scandir($dir)) {
            foreach ($files as $entity) {
                if (preg_match("/^\./", $entity) || dfInArray($entity, $this->_exceptions)) {
                    continue;
                }

                if (is_dir($dir . DIRECTORY_SEPARATOR . $entity)) {
                    $this->read($dir . DIRECTORY_SEPARATOR . $entity);
                }

                // Process PHP files
                if (preg_match("/\.php$/", $entity)) {
                    $file = file_get_contents($dir . DIRECTORY_SEPARATOR . $entity);

                    // Skip files with "Autoloader: disable" comment
                    if (preg_match("/\/\/[\s]{1,}Autoloader\:[\s]{1,}disable/", $file)) {
                        continue;
                    } else {
                        // Strip comments for faster parsing
                        $file = preg_replace([
                            "/\/\/.*?\n/",
                            "/\n/",
                            "/\/\*.*?\*\//"
                        ], [
                            '',
                            '',
                            ''
                        ], $file);

                        $m0 = false;

                        // Find class declaration
                        if (preg_match("/class[\s]{1,}([\w\d]{1,})/i", $file, $m01)) {
                            $m0 = $m01;
                        }

                        // Find interface declaration (AUTOLOAD_MODE 1)
                        if (AUTOLOAD_MODE == 1 && preg_match("/interface[\s]{1,}([a-z0-9_]{1,})/i", $file, $m02)) {
                            $m0 = $m02;
                        }

                        // Find trait declaration (AUTOLOAD_MODE 1)
                        if (AUTOLOAD_MODE == 1 && preg_match("/trait[\s]{1,}([a-z0-9_]{1,})/i", $file, $m03)) {
                            $m0 = $m03;
                        }

                        if (false !== $m0) {
                            // Extract namespace
                            preg_match("/namespace[\s]{1,}([^\s]{1,})\;/", $file, $m1);

                            // Extract parent class (extends)
                            if (preg_match("/extends[\s]{1,}([a-z0-9_]{1,})/i", $file, $m2)) {
                                $this->_map['\\' . $m1[1] . '\\' . $m0[1]] =
                                    preg_match("/[^a-zA-Z]{1}/", $m2[1]) ? $m2[1] : "\\" . $m1[1] . "\\" . $m2[1];
                            }

                            // Track class and its load status
                            $this->_load['\\' . $m1[1] . '\\' . $m0[1]] = false;
                            $this->_classes['\\' . $m1[1] . '\\' . $m0[1]] =
                                '"\\' . $m1[1] . '\\' . $m0[1] . '"=>"'
                                . __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..'
                                . str_replace($this->_baseDir, '', $dir) . DIRECTORY_SEPARATOR . $entity . '"';
                        }
                    }
                }
                // Process static files (CSS, JS, maps)
                else if (preg_match("/\.js$/", $entity) || preg_match("/\.css$/", $entity) || preg_match("/\.map$/", $entity)) {
                    $path = str_replace($this->_baseDir, '', $dir) . DIRECTORY_SEPARATOR . $entity;
                    $this->_static[$path] = md5($dir . DIRECTORY_SEPARATOR . $entity . filectime($dir . DIRECTORY_SEPARATOR . $entity));
                }
            }
        }
    }
}

// Execute the autoloader generation
new Autoloader();