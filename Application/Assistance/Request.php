<?php

/**
 * HTTP request parser and parameter handler.
 *
 * This class processes incoming HTTP requests by:
 * - Parsing the request URI into module/controller/action
 * - Handling URL rewriting and short links
 * - Managing GET, POST, PUT, and DELETE parameters
 * - Validating security signatures (CSRF protection)
 * - Processing JSON input streams
 * - Providing parameter validation infrastructure
 *
 * The Request object is passed to controllers and contains all
 * data needed to handle the current HTTP request.
 *
 * @package   Application\Assistance
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Assistance;

use Application\Helpers\Line;
use \Config\CC as C;

class Request
{
    /** @var string Session key for CSRF verification hash */
    const _VERIFY_ = 'verifyHash';

    /** @var bool Flag indicating if request method is GET */
    protected $_isGet = false;

    /** @var bool Flag indicating if request method is POST */
    protected $_isPost = false;

    /** @var bool Flag indicating if request method is PUT */
    protected $_isPut = false;

    /** @var bool Flag indicating if request method is DELETE */
    protected $_isDelete = false;

    /** @var string|null Requested module name */
    protected $_module;

    /** @var string|null Requested controller name */
    protected $_controller;

    /** @var string|null Requested action name */
    protected $_action;

    /** @var string|null A/B testing variant identifier */
    public $_ab;

    /** @var bool Flag indicating if this is a reload request */
    public $_reload;

    /** @var string Original request URL */
    public $requestUrl;

    /** @var bool Whether guests are allowed to access this module */
    public $_allow_guest = false;

    /** @var array Request parameters (merged from all sources) */
    public $_params = [];

    /** @var array GET parameters */
    public $_get = [];

    /** @var array POST parameters */
    public $_post = [];

    /** @var array PUT parameters */
    public $_put = [];

    /** @var array DELETE parameters */
    public $_delete = [];

    /** @var string Client IP address */
    public $_ip;

    /** @var string|null API version extracted from URL */
    public $_version;

    /** @var bool Whether this is a request to the default page */
    public $_defaultLink = false;

    /** @var bool Whether form validation should be ignored (signature failure) */
    public $_ignoreForm = false;

    /**
     * Parse and process the incoming HTTP request.
     *
     * Performs the following operations:
     * 1. Parses the request URI into module/controller/action
     * 2. Handles short links and URL normalization
     * 3. Validates CSRF signature for POST requests
     * 4. Processes JSON input streams
     * 5. Detects HTTP method (GET/POST/PUT/DELETE)
     * 6. Merges all request data into unified parameters
     */
    public function __construct()
    {
        // ====================================================================
        // URI PARSING AND NORMALIZATION
        // ====================================================================

        /**
         * Split the request URI by '?' to separate path from query string.
         * The 'request' parameter contains the full URI path.
         */
        $request = dfExplode('?', $_REQUEST['request']);
        $this->requestUrl = $_REQUEST['request'];

        /**
         * Remove file extensions (.css, .js, .map) from the URI.
         * These are handled as static file requests.
         */
        if (preg_match("/(\.(css|js|map))$/", $request[0], $m)) {
            $request[0] = preg_replace("/(\.(css|js|map))$/", '', $request[0]);
        }

        /**
         * Extract API version from URL pattern: /v1/controller/action
         * The version is stored for API routing purposes.
         */
        if (preg_match("/v([\d]{1,})[\/]{0,}/", $request[0], $v)) {
            $request[0] = preg_replace("/^v([\d]{1,})\//", '', $request[0]);
            $this->_version = $v[1];
        }

        // ====================================================================
        // SHORT LINK RESOLUTION
        // ====================================================================

        /**
         * Initialize the short link system.
         * This allows aliases like /about to map to /Free/Page/view/1
         */
        \Application\Helpers\Line::setShortLinks();

        /**
         * Handle font file requests.
         * Converts /Free/Scope/fonts/fontname.woff to /Free/Scope/fonts/file/fontname.woff
         * This routes font requests through the controller for proper MIME handling.
         */
        if (preg_match("/" . \Modules\Free\Bootstrap::$_module . '\/' . \Modules\Free\Crud::SCOPE_CONTROLLER . "\/fonts\/([-_\w\d]{1,}\.[\w\d]{1,})/", $request[0], $m)) {
            $request[0] = \Modules\Free\Bootstrap::$_module . '/' . \Modules\Free\Crud::SCOPE_CONTROLLER . '/fonts/file/' . $m[1];
        }
        /**
         * Block direct file access attempts (e.g., /config.php, /.htaccess).
         * Logs the attempt and redirects the client to their own IP.
         */
        else if (preg_match("/\./", $request[0]) && !dfInArray($request[0], \Application\Helpers\Line::$shortLinks[1])) {
            $this->callBrutus($this->requestUrl);
        }

        /**
         * Normalize the URL by removing duplicate slashes, trailing slashes, etc.
         */
        $request[0] = \Application\Helpers\Line::normalizeURL($request[0]);

        /**
         * Block requests to /Payment/ endpoints.
         * Returns a 404 response immediately.
         */
        if (preg_match("/^Payment/", $request[0])) {
            header("HTTP/1.0 404 Not Found");
            exit;
        }

        // ====================================================================
        // URL DECOMPOSITION INTO MODULE/CONTROLLER/ACTION
        // ====================================================================

        /**
         * Split the URL path by slash.
         * Format: module/controller/action/param1/value1/param2/value2
         */
        $res = dfExplode('/', preg_replace("/\/$/", '', preg_replace('/\s/', '', $request[0])));

        /** @var \Modules\Base\Crud $crud */

        /**
         * Handle stale navigation: if the user has a last URL cookie
         * and the current request is incomplete (missing controller/action),
         * redirect to the stored URL.
         */
        if ((!isset($res[1]) || empty($res[1]) || !isset($res[2]) || empty($res[2])) && isset($_COOKIE[\Application\Helpers\Person::LAST_URL_COOKIE . CURRENT_PROJECT])) {
            $url = $_COOKIE[\Application\Helpers\Person::LAST_URL_COOKIE . CURRENT_PROJECT];
            setcookie(\Application\Helpers\Person::LAST_URL_COOKIE . CURRENT_PROJECT, '', -1, '/');
            \Application\Helpers\Line::jump(ROOT . $url);
        }

        /**
         * Detect request to the root URL (no path specified).
         * This will be handled as the default page.
         */
        if ((!isset($res[0]) || empty($res[0])) && !isset($res[1]) && !isset($res[2])) {
            $this->_defaultLink = true;
        }

        /**
         * Extract module, controller, and action from the URL segments.
         * If any segment is missing, fall back to defaults from configuration.
         */
        $module = ucfirst(!isset($res[0]) || empty($res[0])
            ? C::get('default_module')
            : $res[0]);

        $controller = ucfirst(!isset($res[1]) || empty($res[1])
            ? C::get('default_controller')
            : $res[1]);

        $action = strtolower((!isset($res[2]) || empty($res[2]))
            ? C::get('default_action')
            : $res[2]);

        $this->_module = $module;
        $this->_controller = $controller;
        $this->_action = $action;

        // ====================================================================
        // CSRF SIGNATURE VALIDATION
        // ====================================================================

        /**
         * Check request trust level (for cross-project authentication).
         */
        $trust = isset($_REQUEST['trust']) ? Line::checkTrust($_REQUEST['trust']) : false;

        /**
         * Validate CSRF signature for POST requests.
         *
         * If signature validation is enabled (IGNORE_SIGN == false):
         * - The _sign_ parameter must match the session's verifyHash
         * - If validation fails, the request is rejected and all POST data is cleared
         * - This prevents cross-site request forgery attacks
         *
         * A new signature is generated after each request.
         */
        if (false === $trust && isset($_REQUEST['request']) && !preg_match("/(css|js)$/", $_REQUEST['request'])) {
            if (!isset($_SESSION['ignore_sign']) && IGNORE_SIGN === false && (dfCount($_POST) > 0)) {
                if (!isset($_SESSION['ignore_sign']) && (!isset($_REQUEST['_sign_']) || !isset($_SESSION[self::_VERIFY_]) || $_SESSION[self::_VERIFY_] != $_REQUEST['_sign_'])) {
                    $this->_ignoreForm = true;
                    foreach ($_POST as $k => $v) {
                        if ($k !== 'request') {
                            unset($_REQUEST[$k]);
                        }
                        unset($_POST[$k]);
                    }
                }
            }
            $_SESSION[self::_VERIFY_] = md5(time() . rand(1000, 9999));
            unset($_SESSION['ignore_sign']);
        }

        // ====================================================================
        // URL PARAMETER EXTRACTION
        // ====================================================================

        /**
         * Extract key-value pairs from the URL path after the action segment.
         * Format: param1/value1/param2/value2
         * These are added to the unified parameters array.
         */
        if (isset($res[3])) {
            $cnt = dfCount($res);
            for ($i = 3; $i < $cnt; $i++) {
                $this->_params[$res[$i++]] = isset($res[$i]) ? \Application\Helpers\Line::unescape($res[$i]) : null;
            }
        }

        // ====================================================================
        // CLIENT IP DETECTION
        // ====================================================================

        /**
         * Determine the client IP address.
         * Falls back to 127.0.0.1 for CLI or missing server variables.
         */
        $this->_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';

        // ====================================================================
        // MERGE ALL REQUEST PARAMETERS
        // ====================================================================

        /**
         * Initially set GET flag if we have URL parameters.
         * These will be refined after HTTP method detection.
         */
        $this->_isGet = !empty($this->_params);
        $this->_get = $this->_params;

        /**
         * Remove the 'request' parameter to avoid conflicts.
         * It's no longer needed after parsing.
         */
        unset($_REQUEST['request']);

        /**
         * Sanitization callback for request parameters.
         * Unescapes HTML entities in all string values.
         */
        $function = function ($v) {
            return is_array($v) ? $v : \Application\Helpers\Line::unescape($v);
        };

        /**
         * Merge all request data (GET, POST, and URL parameters) into _params.
         * _GET and POST take precedence over URL parameters when keys conflict.
         */
        $this->_params = dfArrayMerge(dfArrayMap($function, $_REQUEST), $this->_params);

        // ====================================================================
        // JSON INPUT PROCESSING
        // ====================================================================

        /**
         * Read raw input stream (php://input).
         * This handles PUT, DELETE, and JSON requests that don't use form data.
         */
        $input = file_get_contents('php://input');
        if (!empty($input)) {
            /**
             * Try to parse as JSON first.
             * Common for REST API requests.
             */
            if (is_string($input)) {
                $inputJson = dfJsonDecode($input, true);
            } else {
                $inputJson = false;
            }

            if (is_array($inputJson)) {
                foreach ($inputJson as $k => $v) {
                    $this->{$var}[$k] = $v;
                    $this->_params[$k] = $v;
                }
            } else {
                /**
                 * Fallback: parse as application/x-www-form-urlencoded.
                 * Split by & then by = to extract key-value pairs.
                 */
                parse_str($input, $check);
                if (is_array($check)) {
                    foreach ($check as $k => $v) {
                        $this->_params[$k] = $v;
                    }
                } else {
                    $input = dfExplode('&', $input);
                    foreach ($input as $v) {
                        $v0 = dfExplode('=', $v);
                        $this->{$var}[$v0[0]] = $v0[1];
                        $this->_params[$v0[0]] = $v0[1];
                    }
                }
            }
        }

        // ====================================================================
        // HTTP METHOD DETECTION
        // ====================================================================

        /**
         * Determine the HTTP method from the request.
         * Defaults to GET if no method is specified.
         */
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';

        /**
         * POST detection:
         * - Check REQUEST_METHOD == 'POST'
         * - OR check if $_POST is not empty
         * - Ignored if form validation failed (signature mismatch)
         */
        $this->_isPost = $this->_ignoreForm === true ? false : $method == \Application\Assistance\Controller\RestfulController::IS_POST || dfCount($_POST) > 0;
        $this->_post = $this->_ignoreForm === true ? [] : $_POST;

        /**
         * GET detection:
         * - Check REQUEST_METHOD == 'GET'
         * - OR check if $_GET is not empty
         */
        $this->_isGet = $method == \Application\Assistance\Controller\RestfulController::IS_GET || dfCount($_GET) > 0;

        /**
         * DELETE and PUT detection based on REQUEST_METHOD.
         */
        $this->_isDelete = $method == \Application\Assistance\Controller\RestfulController::IS_DELETE;
        $this->_isPut = $method == \Application\Assistance\Controller\RestfulController::IS_PUT;

        /**
         * Merge GET parameters with URL parameters.
         */
        $this->_get = dfArrayMerge($this->_get, $_GET);

        /**
         * Check if this is a reload request.
         * Determined by comparing the referer URL with the current module/controller/action.
         */
        $this->_reload = !empty($_SERVER['HTTP_REFERER'])
            && preg_match("/" . implode('\/', [$module, $controller, $action]) . "$/", $_SERVER['HTTP_REFERER']) == 1;
    }

    /**
     * Log unauthorized access attempt and redirect.
     *
     * Records the attempted URL in the brutus table for security monitoring.
     * Redirects the client to their own IP address to discourage scanning.
     *
     * @param string $url The unauthorized URL that was accessed
     */
    public function callBrutus($url)
    {
        if (!$brutus = \Modules\Base\Models\DbTables\Brutus::getByUrl($url)) {
            $brutus = (new \Modules\Base\Models\Brutus())
                ->setBrutusUrl($url)
                ->setBrutusCounter(0);
        }
        $brutus->increment();
        header('Location: http://' . $_SERVER['REMOTE_ADDR'] . '/');
        exit;
    }

    /**
     * Get the current module name.
     *
     * @return string
     */
    public function getModule()
    {
        return $this->_module;
    }

    /**
     * Set the current module name.
     *
     * @param string $module Module name
     *
     * @return $this
     */
    public function setModule($module)
    {
        $this->_module = $module;
        return $this;
    }

    /**
     * Get the current controller name.
     *
     * @return string Controller name, or default from config if not set
     */
    public function getController()
    {
        return is_null($this->_controller) ? C::get('default_controller') : $this->_controller;
    }

    /**
     * Set the current controller name.
     *
     * @param string $controller Controller name
     *
     * @return $this
     */
    public function setController($controller)
    {
        $this->_controller = $controller;
        return $this;
    }

    /**
     * Get the current action name.
     *
     * @return string
     */
    public function getAction()
    {
        return $this->_action;
    }

    /**
     * Set the current action name.
     *
     * Used primarily when redirecting to the login view.
     *
     * @param string $action Action name
     *
     * @return $this
     */
    public function setAction($action)
    {
        $this->_action = $action;
        return $this;
    }

    /**
     * Check if the current request method is POST.
     *
     * @return bool
     */
    public function isPost()
    {
        return $this->_isPost;
    }

    /**
     * Retrieve request parameters.
     *
     * @param string|array|null $key     Parameter key to retrieve, or array of keys
     * @param string|int|bool   $default Default value if parameter not found
     *
     * @return array|string|int|bool All parameters, specific parameter, or default
     */
    public function getParams($key = null, $default = false)
    {
        if (is_array($key)) {
            /** @var array $default */
            return dfArrayMerge(array_fill_keys($key, $default), $this->_params);
        }
        return is_null($key) ? $this->_params : (isset($this->_params[$key]) ? $this->_params[$key] : $default);
    }
}