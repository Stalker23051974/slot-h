<?php

/**
 * cURL HTTP client wrapper.
 *
 * Provides a fluent interface for making HTTP requests using cURL.
 * Supports GET, POST, PUT, DELETE methods with:
 * - Custom headers
 * - Request data (query string or form data)
 * - Response handling with JSON decoding
 * - Error detection and reporting
 * - cURL info retrieval
 *
 * Usage example:
 *   $curl = new Curl();
 *   $result = $curl->setUrl('https://api.example.com/users')
 *                  ->setData(['id' => 123])
 *                  ->setHeader(['Authorization: Bearer token'])
 *                  ->get();
 *
 * @package   Application\Helpers
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Helpers;

class Curl
{
    /** Error key for cURL failures */
    const CURL_ERROR = 'curl_error';

    /** @var resource cURL instance */
    protected $_instance;

    /** @var array cURL request info */
    protected $_info;

    /**
     * Initialize a new cURL session.
     */
    public function __construct()
    {
        $this->_instance = curl_init();
    }

    /** @var bool Whether to use POST method (true) or GET (false) */
    private $_post = true;

    /**
     * Set request method to POST.
     *
     * @return $this
     */
    public function setPost()
    {
        $this->_post = true;
        return $this;
    }

    /**
     * Set request method to GET.
     *
     * @return $this
     */
    public function setGet()
    {
        $this->_post = false;
        return $this;
    }

    /** @var string|null Target URL */
    private $_url = null;

    /**
     * Set the target URL for the request.
     *
     * @param string $url Request URL
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->_url = $url;
        return $this;
    }

    /** @var array Request data (query string for GET, form data for POST) */
    private $_data = [];

    /**
     * Set all request data.
     *
     * @param array $data Key-value pairs
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->_data = $data;
        return $this;
    }

    /**
     * Add a single key-value pair to request data.
     *
     * @param string $key   Data key
     * @param mixed  $value Data value
     */
    public function addData($key, $value)
    {
        $this->_data[$key] = $value;
    }

    /** @var array HTTP headers */
    private $_header = [];

    /**
     * Set multiple HTTP headers.
     *
     * @param array $data Array of header strings
     *
     * @return $this
     */
    public function setHeader($data)
    {
        foreach ($data as $v) {
            $this->addHeader($v);
        }
        return $this;
    }

    /** @var array Default cURL options */
    private $_options = [
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false
    ];

    /**
     * Add a single HTTP header.
     *
     * @param string $value Header string (e.g., "Content-Type: application/json")
     */
    public function addHeader($value)
    {
        $this->_header[dfCount($this->_header) + 1] = $value;
    }

    /**
     * Get cURL request info after execution.
     *
     * @return array cURL info (url, http_code, total_time, etc.)
     */
    public function getInfo()
    {
        return $this->_info;
    }

    /**
     * Execute a GET request.
     *
     * Appends data as query string to the URL.
     *
     * @return array|mixed Decoded JSON response or error array
     */
    public function get()
    {
        $this->_url .= '?' . Line::generateParamsString($this->_data);
        if (dfCount($this->_header) > 0) {
            $this->_header[0] = 'GET HTTP/1.0';
        }
        return $this->response();
    }

    /**
     * Execute a POST request.
     *
     * Sends data as application/x-www-form-urlencoded.
     *
     * @return array|mixed Decoded JSON response or error array
     */
    public function post()
    {
        $this->addOptions([
            CURLOPT_POST => $this->_post,
            CURLOPT_POSTFIELDS => http_build_query($this->_data)
        ]);
        if (dfCount($this->_header) > 0) {
            $this->_header[0] = 'POST HTTP/1.0';
        }
        return $this->response();
    }

    /**
     * Execute a PUT request.
     *
     * Sends data as application/x-www-form-urlencoded.
     *
     * @return array|mixed Decoded JSON response or error array
     */
    public function put()
    {
        $this->addOptions([
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => http_build_query($this->_data)
        ]);
        if (dfCount($this->_header) > 0) {
            $this->_header[0] = 'POST HTTP/1.0';
        }
        return $this->response();
    }

    /**
     * Execute a DELETE request.
     *
     * Sends data as JSON with appropriate Content-Type header.
     *
     * @return array|mixed Decoded JSON response or error array
     */
    public function delete()
    {
        $this->addOptions([
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_POSTFIELDS => http_build_query($this->_data),
            CURLOPT_HTTPHEADER => 'Content-Type: application/json',
            'Content-Length: ' . strlen(dfJsonEncode($this->_data))
        ]);
        if (dfCount($this->_header) > 0) {
            // Header handling is reserved for future use
        }
        return $this->response();
    }

    /**
     * Execute the cURL request and process the response.
     *
     * Applies all options, executes the request, and attempts to
     * decode the response as JSON. If decoding fails, returns
     * an error array with the raw data.
     *
     * @return array|mixed Decoded JSON or error array
     */
    private function response()
    {
        $options = [CURLOPT_URL => $this->_url];

        if (dfCount($this->_header) > 0) {
            $options[CURLOPT_HTTPHEADER] = $this->_header;
        }

        $this->addOptions($options);

        foreach ($this->_options as $k => $v) {
            curl_setopt($this->_instance, $k, $v);
        }

        $jsonData = curl_exec($this->_instance);
        $this->_info = curl_getinfo($this->_instance);
        curl_close($this->_instance);

        $result = dfJsonDecode($jsonData, true);
        return $result ? $result : [
            self::CURL_ERROR => json_last_error(),
            'message' => json_last_error_msg(),
            'data' => $jsonData
        ];
    }

    /**
     * Add or override cURL options.
     *
     * @param array $options Associative array of CURLOPT_* => value
     *
     * @return $this
     */
    public function addOptions($options)
    {
        foreach ($options as $k => $v) {
            $this->_options[$k] = $v;
        }
        return $this;
    }

    /**
     * Execute a system-level curl command via passthru.
     *
     * Used for gateway/proxy operations where the system's
     * curl binary is preferred over the PHP extension.
     *
     * @param string      $method HTTP method (GET, POST, etc.)
     * @param string      $addr   Target URL
     * @param string|null $params Request parameters (for POST)
     */
    public function gate($method, $addr, $params = null)
    {
        return passthru('curl -X' . $method . ' "' . $addr . '"'
            . (is_null($params) ? '' : "-d'" . $params . "'"));
    }
}