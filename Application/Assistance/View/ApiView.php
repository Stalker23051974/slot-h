<?php

/**
 * JSON API view renderer.
 *
 * This class handles output for API controllers, providing:
 * - JSON response formatting with consistent structure
 * - CORS headers for cross-origin requests
 * - Cache control headers for API responses
 * - Session signature and checksum for security
 * - User context (person data) for API operations
 *
 * API responses include:
 * - Status: success/error indicators
 * - Data: the actual response payload
 * - Message: optional user-facing message
 * - Checksum: session identifier for validation
 * - Sign: CSRF token for request verification
 *
 * @package   Application\Assistance\View
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Assistance\View;

class ApiView
{
    /**
     * The result data to be returned as JSON.
     *
     * @var mixed
     */
    public $_result;

    /**
     * Whether to format the response as JSON.
     *
     * When true, the Content-Type header is set to application/json.
     *
     * @var bool
     */
    public $_json = false;

    /**
     * Fingerprint token for security validation.
     *
     * @var bool|string
     */
    public $_fp_ = false;

    /**
     * Current authenticated user object.
     *
     * @var bool|\Modules\Base\Models\Person
     */
    public $currentPerson = false;

    /**
     * Current user's protected data (voice, preferences, etc.).
     *
     * @var bool|\Modules\Base\Models\PersonProtected
     */
    public $currentProtected = false;

    /**
     * Success status string for API responses.
     *
     * @var string
     */
    const STATUS_SUCCESFUL = 'data';

    /**
     * Current user's private data (login, hash, etc.).
     *
     * @var mixed
     */
    public $currentPrivate;

    /**
     * Render and output the API response.
     *
     * This method:
     * 1. Sets CORS headers for cross-origin access
     * 2. Sets cache headers (expires in 2 days)
     * 3. Sets JSON content type if enabled
     * 4. Formats the response with security tokens
     * 5. Encodes and outputs JSON
     *
     * The response includes:
     * - Checksum: API session key from request
     * - Sign: Current verification hash from session
     * - All data from $_result
     */
    public function show()
    {
        /**
         * Enable cross-origin resource sharing.
         * Allows any domain to access this API endpoint.
         */
        header('Access-Control-Allow-Origin: *');

        /**
         * Set cache headers to 2 days.
         * Reduces unnecessary requests for static API data.
         */
        header('expires: ' . gmdate("D, d M Y H:i:s", CURRENT_TIME + 172800) . ' GMT');

        /**
         * Set JSON content type if JSON mode is enabled.
         */
        if (true === $this->_json) {
            header('Content-Type: application/json; charset: UTF-8');
        }

        /**
         * Ensure result is an array.
         * Wrap scalar results in an array structure.
         */
        if (!is_array($this->_result)) {
            $this->_result = ['_result_' => $this->_result];
        }

        /**
         * Add security tokens to the response.
         * - checksum: API session key for validation
         * - sign: CSRF token for request verification
         */
        $this->_result['checksum'] = $_REQUEST[\Application\Assistance\Controller\ApiController::API_CHECK_SESSION_KEY];
        $this->_result['sign'] = $_SESSION[\Application\Assistance\Request::_VERIFY_];

        /**
         * Encode and output the JSON response.
         */
        echo dfJsonEncode($this->_result);

        /**
         * Mark API mode as active for logging and debugging.
         */
        \Config\CC::$_api = true;
    }
}