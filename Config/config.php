<?php

/**
 * Global configuration and core bootstrap functionality.
 *
 * This file defines all system-wide constants, loads project-specific
 * INI configurations, and provides the CC (Core Controller) class
 * for configuration management, localization, icons, and autoloading.
 *
 * @package   Config
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Config;

use \Modules\Base\Models\DbTables as db;

// ============================================================================
// BOOTSTRAP & ENVIRONMENT SETUP
// ============================================================================

/** @var string|null $cliStage CLI mode stage identifier (injected via command line) */
require_once('php8_connector.php');

/**
 * Enable developer mode flag.
 * When true, enables XHProf profiling, detailed error output, and debugging tools.
 */
define('DEVELOPER_MODE', false);

/**
 * Extract the calling domain from server variables.
 * Handles proxy forwarding (HTTP_X_FORWARDED_HOST) and standard HTTP_HOST.
 * Removes 'www.' prefix for normalization.
 */
define(
    'CALLING_DOMEN',
    preg_replace(
        "/^www\./",
        '',
        (
        isset($_SERVER['HTTP_X_FORWARDED_HOST'])
            ? $_SERVER['HTTP_X_FORWARDED_HOST']
            : (
        isset($_SERVER['HTTP_HOST'])
            ? $_SERVER['HTTP_HOST']
            : (
        isset($_SERVER['SERVER_NAME'])
            ? (
        $_SERVER['SERVER_NAME'] == $_SERVER['SERVER_ADDR']
            ? $_SERVER['SERVER_ADDR'] . '.' . $_SERVER['SERVER_PORT']
            : $_SERVER['SERVER_NAME']
        )
            : ''
        )
        )
        )
    )
);

/**
 * Current project identifier.
 * Retrieved from CLI configuration if running in console mode.
 */
define('CURRENT_PROJECT', CC::get('cli_project_id'));

/** @var string|null $projectAlias Project alias for multi-project routing */
$projectAlias = CC::get('cli_project_alias');

/**
 * Active project identifier.
 * Uses CLI alias if provided, otherwise falls back to CURRENT_PROJECT.
 */
define('USE_PROJECT', $projectAlias ? $projectAlias : CURRENT_PROJECT);

/**
 * Base design path for layout templates.
 */
define('BASE_DESIGN', DIRECTORY_SEPARATOR . CC::get('design'));
define('BASE_DESIGN_PATH', '\\' . CC::get('design'));

/**
 * Root URL of the application (protocol + domain + trailing slash).
 */
define('ROOT', CC::get('protocol') . '://' . CALLING_DOMEN . '/');

// ============================================================================
// LOGGING & DEBUGGING PATHS
// ============================================================================

/** Directory path for debug logs (project-specific) */
define('DEBUG_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'Logs' . DIRECTORY_SEPARATOR . CONF_NAME . DIRECTORY_SEPARATOR);

/** Subdirectory for report logs */
define('REPORT_DEBUG_LOG', 'reports' . DIRECTORY_SEPARATOR);

/** Subdirectory for API request logs */
define('API_LOG', 'api' . DIRECTORY_SEPARATOR);

/** Subdirectory for REST API request logs */
define('REST_API_LOG', 'rest_api' . DIRECTORY_SEPARATOR);

// ============================================================================
// SECURITY & CRYPTOGRAPHY
// ============================================================================

/** Gateway/proxy mode flag (disabled by default) */
define('GATE', false);

/** Cryptographic hashing algorithm for signatures and tokens */
define('CRYPTO_PROTOCOL', 'MD5');

/** Salt phrase used for password complexity validation */
define('PASSWORD_CHECK_PHRASE', 'siteper100rub');

// ============================================================================
// PAGINATION & LISTING
// ============================================================================

/** Default number of rows per page in list views */
define('ROW_ON_PAGE', 20);

// ============================================================================
// AUTOLOADER CONFIGURATION
// ============================================================================

/**
 * Autoloader mode:
 * 0 - Load all classes on every session
 * 1 - Load only interfaces & traits initially, others on demand (recommended)
 */
define('AUTOLOAD_MODE', 1);

// ============================================================================
// REPORTING LIMITS
// ============================================================================

/** Maximum number of report entries allowed */
define('MAX_COUNT_REPORT', 5000);

// ============================================================================
// FILE SYSTEM PATHS
// ============================================================================

/** Absolute path to the project root directory */
define('ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

/** Default preview image dimensions (for thumbnails) */
define('PHOTO_PREVIEW_WIDTH', 125);
define('PHOTO_PREVIEW_HEIGHT', 215);

/** Standard image dimensions (for full-size images) */
define('PHOTO_WIDTH', 600);
define('PHOTO_HEIGHT', 600);

/** Storage directory name */
define('FOLDER_STORAGE', 'Storage');

/** Absolute path to the storage directory */
define('FILE_STORAGE', ROOT_PATH . FOLDER_STORAGE);

/** Report storage subdirectory */
define('REPORT_STORAGE', DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR);

/** Document storage subdirectory */
define('DOCUMENT_STORAGE', DIRECTORY_SEPARATOR . 'documents' . DIRECTORY_SEPARATOR);

/** Media/image storage subdirectory */
define('IMAGE_STORAGE', DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR);

/** Migrations directory path (relative to project root) */
define('MIGRATION_PATH', 'Config' . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR);

/** Temporary files directory */
define('TEMP_PATH', 'Config' . DIRECTORY_SEPARATOR . 'Tmp' . DIRECTORY_SEPARATOR);

/** Database dump directory */
define('DUMP_PATH', 'Config' . DIRECTORY_SEPARATOR . 'Dump' . DIRECTORY_SEPARATOR);

/** Chunk size for file uploads (5 MB) */
define('FILE_UPLOAD_CHANK_SIZE', 5242880);

// ============================================================================
// DEBUG & OUTPUT SETTINGS
// ============================================================================

/** Enable debug mode (affects error display and logging verbosity) */
define('DEBUG', true);

/** End-of-line delimiter for HTML output (line break with <br>) */
define('D_EOL', "<br>\n");

/** END CUSTOM DEFAULTS */
/** END BILLING DEFAULTS */

/** Default module name for the base application layer */
define('DEFAULT_BASE_MODULE', 'Base');

/** Default controller name for the base application layer */
define('DEFAULT_BASE_CONTROLLER', 'Person');

/** Enable JavaScript obfuscation for production builds (disabled by default) */
define('OBFUSCATE_JS', false);

// ============================================================================
// QUERY DEBUGGING FLAGS
// ============================================================================

/**
 * GET/POST/PUT parameter name that triggers SQL query logging.
 * Setting this parameter to any value will output all database queries.
 * It is recommended to change this value from the default to avoid
 * unintended exposure to third parties.
 */
define('SHOW_QUERY_FLAG', 'showmequeries');

/** Enable maintenance mode (displays "Engineering Works" page) */
define('ENGINEERING_WORKS', false);

/**
 * Global query debug mode.
 * When true, shows all SQL queries regardless of SHOW_QUERY_FLAG.
 * Overrides the per-request flag for global debugging.
 */
define('SHOW_QUERIES', false);

/**
 * Security signature validation.
 * When true, disables frontend signature verification (development use only).
 * Warning: Disabling this reduces security significantly.
 */
define('IGNORE_SIGN', false);

/** Enable stack trace output for exceptions and errors */
define('SHOW_TRACE', false);

/** Current UNIX timestamp (used for consistency across requests) */
define('CURRENT_TIME', time());

// ============================================================================
// CONFIGURATION INITIALIZATION
// ============================================================================

/**
 * Load project configuration.
 * Web mode: uses domain from HTTP_HOST.
 * CLI mode: uses injected stage parameter.
 */
if (isset($_SERVER['HTTP_HOST'])) {
    CC::get();
} elseif (isset($cliStage) && !is_null($cliStage)) {
    CC::get(null, $stage = $cliStage);
}

// ============================================================================
// CC CLASS - CORE CONTROLLER / CONFIGURATION MANAGER
// ============================================================================

/**
 * Core Controller class.
 *
 * Provides centralized access to:
 * - Project configuration (INI files)
 * - Dynamic constants (database-driven)
 * - Localization/translation
 * - Icon management
 * - Autoloader integration
 *
 * This is the primary configuration hub for the entire application.
 */
final class CC
{
    /** @var bool Flag indicating if the last URL was stored for navigation */
    public static $_setLastUrl = false;

    /** @var \StdClass Configuration object containing all INI values */
    private static $_config;

    /** @var bool API mode flag */
    public static $_api = false;

    /** @var string|null Current log file name */
    public static $_logFile = null;

    /** @var array|null Cached database constants */
    public static $_constants = null;

    /** @var \Application\Autoloader Autoloader instance */
    public static $_loader = null;

    /** @var array|null Cached icon data */
    public static $_icons = null;

    /** @var array Trusted IPs/hosts (reserved for future use) */
    public static $_trusted = [];

    /** @var mixed Language data (reserved for future use) */
    public static $_lng = null;

    /**
     * Retrieve project configuration.
     *
     * Parses the INI file for the current domain or CLI stage and stores it
     * in a structured object. Handles database connector configurations
     * with automatic prefixing.
     *
     * @param string|null $property Specific configuration key to retrieve
     * @param string|null $stage    CLI stage name (used in console mode)
     *
     * @return \StdClass|bool Configuration object, specific property value,
     *                        or false if not found
     */
    public static function get($property = null, $stage = null)
    {
        if (!defined('CONF_NAME')) {
            self::$_config = new \StdClass();

            // Determine project name from domain or CLI stage
            if (empty(CALLING_DOMEN) && empty($stage)) {
                $stage = 'cli';
            }

            if (CALLING_DOMEN != '' || !is_null($stage)) {
                // Replace colon with dot for port-based domain detection
                $name = CALLING_DOMEN ? preg_replace("/\:/", '.', CALLING_DOMEN) : $stage;

                // Parse the INI configuration file
                if ($ini = parse_ini_file(__DIR__ . '/Domens' . DIRECTORY_SEPARATOR . $name . '.ini', true)) {
                    define('CONF_NAME', $name);

                    foreach ($ini as $k => $v) {
                        if (is_array($v)) {
                            self::$_config->{$k} = new \StdClass();

                            // Handle database connector configurations
                            if (isset($v['db_name'])) {
                                self::$_config->{'connector_' . $v['db_name']} = new \StdClass();
                            }

                            foreach ($v as $k0 => $v0) {
                                self::$_config->{$k}->{$k0} = $v0;
                                if (isset($v['db_name'])) {
                                    self::$_config->{'connector_' . $v['db_name']}->{$k0} = $v0;
                                }
                            }
                        } else {
                            self::$_config->{$k} = $v;
                        }
                    }
                } else {
                    CC::abort('Unknown project');
                }
            }
        }

        return is_null($property)
            ? self::$_config
            : (!empty(self::$_config->{$property}) ? self::$_config->{$property} : false);
    }

    /**
     * Retrieve a dynamic constant value from the database.
     *
     * Constants are stored in the 'constants' table and can be:
     * - Simple strings/numbers
     * - JSON objects (auto-decoded)
     * - Boolean values (converted from '1'/'0')
     *
     * @param string $k       Constant name/key
     * @param mixed  $default Default value if constant not found
     *
     * @return mixed The constant value, or default if not found
     */
    public static function constant($k, $default = false)
    {
        if (null === self::$_constants) {
            self::$_constants = [];

            if ($res = db\Constant::getAll()) {
                /** @var \Modules\Base\Models\Constant[] $res */
                foreach ($res as $v) {
                    if ($resJson = dfJsonDecode($v->getConstantData(), true)) {
                        $json = isset($resJson[db\Constant::DATA_PARAM_TYPE_JSON]) ? $resJson[db\Constant::DATA_PARAM_TYPE_JSON] : false;
                        $boolean = isset($resJson[db\Constant::DATA_PARAM_TYPE_BOOLEAN]);
                    } else {
                        $json = false;
                        $boolean = false;
                    }

                    self::$_constants[$v->getConstantName()] =
                        true === $json
                            ? dfJsonDecode($v->getConstantValue(), true)
                            : (true === $boolean
                            ? ($v->getConstantValue() == '1' ? true : false)
                            : $v->getConstantValue()
                        );
                }
            }
        }

        return isset(self::$_constants[$k]) ? self::$_constants[$k] : $default;
    }

    /** @var bool|null Translation system availability flag */
    private static $_translate = null;

    /** @var array|null Admin localization cache */
    public static $_adminLocale = null;

    /**
     * Translate a text string to the current user's language.
     *
     * Uses the \Application\Translate system if available.
     * Applies RTL (Right-to-Left) wrapper if the current language requires it.
     *
     * @param string $text The text to translate
     * @param bool   $rtl  Whether to wrap in RTL tags
     *
     * @return string Translated text with optional RTL wrapping
     */
    public static function locale($text, $rtl = true)
    {
        if (null === self::$_translate) {
            self::$_translate = class_exists('\Application\Translate');
        }

        if (true === self::$_translate) {
            if (null !== self::$_adminLocale) {
                $v = \Application\Translate::get($text);
                self::$_adminLocale[md5($text)] = [$text, $v];
                $text = $v;
            } else {
                $text = \Application\Translate::get($text);
            }
        }

        $rtl = $rtl == true && defined('TEXT_DIRECTION') && TEXT_DIRECTION > 0;
        return ($rtl ? '<rtl dir="rtl">' : '') . $text . ($rtl ? '</rtl>' : '');
    }

    /**
     * Retrieve an SVG icon by its constant name.
     *
     * Loads all icons from the database on first call.
     * Returns path data for SVG rendering.
     *
     * @param string $k       Icon constant/identifier
     * @param mixed  $default Default value if icon not found
     *
     * @return mixed Icon path data or default
     */
    public static function icon($k, $default = false)
    {
        if (null === self::$_icons) {
            self::$_icons = [];

            if ($res = db\Icon::getAll()) {
                /** @var \Modules\Base\Models\Icon[] $res */
                foreach ($res as $v) {
                    self::$_icons[$v->getIconConstant()] = dfJsonDecode($v->getIconPath(), true);
                    if ($v->getIconFrontend() > 0) {
                        self::$_iconsJs[$v->getIconConstant()] = self::$_icons[$v->getIconConstant()];
                    }
                }
            }
        }

        return isset(self::$_icons[$k]) ? self::$_icons[$k] : $default;
    }

    /**
     * Terminate the application with optional message.
     *
     * In developer mode, saves XHProf profiling data before exiting.
     *
     * @param bool|string $die If string, outputs it as a message; if true, exits silently
     */
    public static function abort($die = false)
    {
        if (true === DEVELOPER_MODE && function_exists('xhprof_disable')) {
            $xhprofData = xhprof_disable();
            include_once '..' . DIRECTORY_SEPARATOR . 'Application' . DIRECTORY_SEPARATOR . 'Tools' . DIRECTORY_SEPARATOR . 'xhprof' . DIRECTORY_SEPARATOR . 'xhprof_lib.php';
            include_once '..' . DIRECTORY_SEPARATOR . 'Application' . DIRECTORY_SEPARATOR . 'Tools' . DIRECTORY_SEPARATOR . 'xhprof' . DIRECTORY_SEPARATOR . 'xhprof_runs.php';
            $run_id = (new \XHProfRuns_Default())->save_run($xhprofData, "xhprof_test");
        }

        if (false !== $die) {
            echo $die;
        }
    }

    /**
     * Get directories to exclude from certain operations.
     *
     * @return array List of excluded directory names
     */
    public static function getLoadExceptions()
    {
        return [
            'Config',
            'Storage',
            'Migration',
            'Tools'
        ];
    }

    /**
     * Get local IP addresses (reserved for whitelisting).
     *
     * @return array Empty array (reserved for future implementation)
     */
    public static function getLocalIp()
    {
        return [];
    }

    /**
     * Custom autoloader callback.
     *
     * Loads classes using the pre-generated class map from Autoloader.
     * Handles inheritance chains by loading parent classes recursively.
     *
     * @param string $class Fully qualified class name
     */
    public static function __loader($class)
    {
        if (!class_exists($class)) {
            $class = '\\' . $class;
            $map = [$class];

            // Build inheritance chain
            if (isset(CC::$_loader->_map[$class])) {
                $class = CC::$_loader->_map[$class];
                while (!class_exists($class)) {
                    $map[] = $class;
                    if (isset(CC::$_loader->_map[$class])) {
                        $class = CC::$_loader->_map[$class];
                    } else {
                        break;
                    }
                }
            }

            // Debug output
            if (SHOW_QUERIES === true) {
                echo D_EOL . '=================================' . D_EOL
                    . 'Class = "' . $class . '" (' . dfJsonEncode($map) . ')' . D_EOL
                    . '=================================' . D_EOL;
            }

            // Load classes in reverse order (parents first)
            foreach (array_reverse($map) as $v) {
                if (!empty(CC::$_loader->_classes[$v])) {
                    if (file_exists(CC::$_loader->_classes[$v]) && is_file(CC::$_loader->_classes[$v])) {
                        require_once(CC::$_loader->_classes[$v]);
                    } elseif (SHOW_QUERIES === true) {
                        echo D_EOL . '=================================' . D_EOL
                            . 'Class = "' . $class . '": reading error!' . D_EOL
                            . '=================================' . D_EOL;
                    }
                }
            }
        }
    }
}

// ============================================================================
// HELPER FUNCTIONS
// ============================================================================

/**
 * Format a backtrace for human-readable output.
 *
 * Extracts class and method names from the call stack,
 * excluding the current call.
 *
 * @param array $backtrace Debug backtrace from debug_backtrace()
 *
 * @return string HTML-formatted list of calling methods
 */
function showCaller($backtrace)
{
    $list = [];
    unset($backtrace[0]);

    if (!empty($backtrace)) {
        foreach ($backtrace as $item) {
            if (isset($item['class']) && isset($item['function'])) {
                $list[] = 'Class: ' . $item['class'] . ', method ' . $item['function'];
            }
        }
    }

    return count($list) > 0 ? implode('<br>', $list) : 'Unknown';
}