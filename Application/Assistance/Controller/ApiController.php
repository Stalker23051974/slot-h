<?php

/**
 * API controller for JSON-based web services.
 *
 * This controller handles API requests that return JSON responses
 * rather than HTML views. It is designed for AJAX calls, mobile app
 * backends, and third-party integrations.
 *
 * Key characteristics:
 * - Returns JSON responses with standardized status structure
 * - No view rendering (uses ApiView)
 * - Supports cross-origin requests (CORS)
 * - Lightweight session handling
 * - Response structure: {status, data, message}
 *
 * API responses follow a consistent format:
 * - Successful: {status: "data", data: {...}, message: false}
 * - Error: {status: "error", data: [], message: "Error description"}
 *
 * @package   Application\Assistance\Controller
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Assistance\Controller;

use \Application\Assistance\View\ApiView as View;
use \Modules\Geo\Models\DbTables as gDb;
use \Config\CC as C;

class ApiController
{
    use \Application\Assistance\Controller\TraitClass;

    /** @var \Application\Assistance\View\ApiView API view object for JSON output */
    public $_view;

    /** @var \Application\Assistance\Request Request object with all input data */
    public $_request;

    /** @var \Application\Helpers\Person\InterfacePerson Person helper instance */
    public $_personHelper;

    /** @var \Modules\Base\Models\PersonLog User activity log model */
    public $_log;

    /**
     * Base response template for successful API calls.
     *
     * @var array
     */
    protected $_responseTemplateOk = [
        'status'  => View::STATUS_SUCCESFUL,
        'data'    => [],
        'message' => false
    ];

    /** Session key for API authentication */
    const API_CHECK_SESSION_KEY = '_api_key';

    /**
     * Initialize the API controller and process the request.
     *
     * The constructor:
     * 1. Sets up person helper and request references
     * 2. Creates an ApiView for JSON output
     * 3. Checks fingerprint if provided
     * 4. Sets up language preferences
     * 5. Runs module preloader (API-specific permission checks)
     * 6. Executes the requested action
     * 7. Renders the JSON response
     *
     * @param \Application\Assistance\Request $request Parsed request object
     */
    public function __construct($request)
    {
        $this->_personHelper = PERSON_HELPER;
        $this->_request = $request;
        $this->_log = new \Modules\Base\Models\PersonLog();
        $this->_view = new View();

        /**
         * Check fingerprint for security validation.
         */
        if (isset($_REQUEST['_fp_'])) {
            \Application\Helpers\Line::checkFingerprint($this->_view);
        }

        /**
         * Set up language preference for API responses.
         * Language affects any localized content returned via API.
         */
        if (!isset($_COOKIE[Controller::FREE_LANGUAGE_COOKIE])
            || !isset(gDb\Language::getActive()[$_COOKIE[Controller::FREE_LANGUAGE_COOKIE]])) {
            $_COOKIE[Controller::FREE_LANGUAGE_COOKIE] = C::get('core_default_language');
            define('DEFAULT_LANGUAGE', C::get('core_default_language'));
        } else {
            define('DEFAULT_LANGUAGE', $_COOKIE[Controller::FREE_LANGUAGE_COOKIE]);
        }

        /**
         * Run module-specific preloader.
         * Handles API authentication and permission checks.
         */
        $preloader = \Application\Crud::MODULE_FOLDER . '\\' . $request->getModule() . '\\Preloader';
        new $preloader($this->_view, $request);

        /**
         * Execute the requested action method.
         */
        $this->{$request->getAction() . \Application\Assistance\Controller\Controller::ACTION_SUFFIX}();

        /**
         * Render the JSON response.
         */
        $this->_view->show();
    }

    /**
     * Format response for autocomplete/typeahead widgets.
     *
     * Standardizes the response structure expected by jQuery UI
     * and other autocomplete libraries.
     *
     * @param array $data Suggestions array (label => value)
     *
     * @return array Autocomplete-formatted response
     */
    public function setAutocompleteResponse($data = [])
    {
        return ['suggestions' => $data];
    }

    /**
     * Create a successful API response.
     *
     * Wraps the response data in the standard API success structure.
     *
     * @param array      $data    Response data payload
     * @param bool|string $message Optional custom message
     *
     * @return array Standard API response array
     */
    public function setOk($data = [], $message = false)
    {
        /**
         * If data is empty, return an empty object or array
         * based on the JSON encoding preference.
         */
        if (0 === dfCount($data)) {
            $data = true === $this->_view->_json ? new \StdClass() : [];
        }

        $response = $this->_responseTemplateOk;
        $response['data'] = $data;

        if (false !== $message) {
            $response['message'] = $message;
        }

        return $response;
    }

    /**
     * Output a raw value and terminate execution.
     *
     * Used for simple API responses that don't need the full
     * status/data/message structure.
     *
     * @param mixed      $v       Value to output
     * @param bool|string $message Optional message (unused in this implementation)
     */
    public function setLineOk($v = [], $message = false)
    {
        die($v);
    }
}