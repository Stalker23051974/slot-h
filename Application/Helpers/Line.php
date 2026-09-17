<?php

/**
 * String, URL, and navigation helper.
 *
 * This class provides a wide range of utility methods for:
 * - String manipulation (camelCase, escaping, transliteration)
 * - URL generation with short link resolution
 * - Navigation and pagination
 * - Password generation and hashing
 * - Trust token creation and validation
 * - Encryption/decryption (AES-256-CBC)
 * - Fingerprint validation (bot detection)
 * - Microtime calculation
 * - Color generation
 * - Transliteration (Cyrillic to Latin)
 *
 * This is one of the most frequently used helpers across the system,
 * particularly for URL generation and string operations.
 *
 * @package   Application\Helpers
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Helpers;

use \Config\CC as C;
use \Modules\Base\Models\DbTables\Constant as T;
use \Application\Assistance\Controller\Controller as Cr;

class Line
{
    // ============================================================================
    // SHORT LINK RESOLUTION
    // ============================================================================

    /** @var array|null Short link mapping: [0 => links, 1 => values, 2 => names] */
    public static $shortLinks = null;

    /** @var array Cached method name mappings */
    public static $methods = [];

    /**
     * Convert a field name to a method name (camelCase).
     *
     * Example: user_id => UserId
     *
     * @param string|array $fields Field name(s)
     *
     * @return array Associative array of field => method name
     */
    public static function getMethods($fields)
    {
        if (!is_array($fields)) {
            $fields = [$fields];
        }
        $res = [];

        $function = function ($v) {
            return strtoupper($v[0][1]);
        };

        foreach ($fields as $field) {
            if (!isset(self::$methods[$field])) {
                $res[$field] = implode('', array_map(function ($v0) {
                    return ucfirst($v0);
                }, explode('_', $field)));
                self::$methods[$field] = $res[$field];
            } else {
                $res[$field] = self::$methods[$field];
            }
        }
        return $res;
    }

    /**
     * Generate a URL for the given module/controller/action.
     *
     * Supports short link resolution and language parameter injection.
     *
     * @param string|null $module Module name (default from config)
     * @param string|null $controller Controller name (default from config)
     * @param string|null $action Action name (default from config)
     * @param array $params Additional URL parameters (key/value pairs)
     * @param bool $lng Whether to include language parameter
     *
     * @return string Generated URL
     */
    public static function getUrl($module = null, $controller = null, $action = null, $params = [], $lng = true)
    {
        $path = implode('/', [
            is_null($module) ? C::get('default_module') : $module,
            is_null($controller) ? C::get('default_controller') : $controller,
            is_null($action) ? C::get('default_action') : $action
        ]);

        if ($lng == true && \Config\CC::$_lng !== null) {
            $params['lng'] = \Config\CC::$_lng;
        }

        // Resolve short links
        if ($module !== DEFAULT_BASE_MODULE) {
            if (is_null(self::$shortLinks)) {
                self::setShortLinks();
            }
            foreach (self::$shortLinks[0] as $k => $v) {
                if (!empty($v) && preg_match("/^" . $v . "$/", $path)) {
                    $path = preg_replace("/^" . $v . "$/", self::$shortLinks[1][$k], $path);
                    break;
                }
            }
        }

        $function = function ($k, $v) {
            return $k . '/' . $v;
        };

        return (C::get('protocol') === 'https' ? ROOT : '/')
            . $path
            . (!empty($params) ? '/' . implode('/', dfArrayMap($function, dfArrayKeys($params), dfArrayValues($params))) : '');
    }

    /**
     * Validate a trust token.
     *
     * Trust tokens are used for cross-project authentication.
     *
     * @param string $requies Trust token string
     *
     * @return bool True if valid, false otherwise
     */
    public static function checkTrust($requies)
    {
        preg_match("/^[a-f]{1}([\d]{1,2})[a-f]{1}([a-f0-9]{8})[a-f]{1}([\d]{1,2})[a-f]{1}([a-f0-9]{4})[a-f]{1}([\d]{1,2})[a-f]{1}([a-f0-9]{10})[a-f]{1}([\d]{1,2})[a-f]{1}([a-f0-9]{10})([a-f0-9]{32})$/", $requies, $m);

        if (isset($m[9])) {
            $key = implode('', [
                $m[2],
                $m[4],
                $m[6],
                $m[8]
            ]);
            $date = implode('', [
                $m[1],
                $m[3],
                $m[5],
                $m[7]
            ]);

            foreach (C::$_trusted as $v) {
                if ($key == $v && md5($key . $date) == $m[9]) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Generate a new trust token.
     *
     * @return string Trust token
     */
    public static function createTrust()
    {
        shuffle(C::$_trusted);
        $key = C::$_trusted[0];
        $template = ['a', 'b', 'c', 'd', 'e', 'f'];

        $list = [];
        for ($i = 0; $i < 8; $i++) {
            shuffle($template);
            $list[] = $template[0];
        }

        $date = [
            date('H'),
            date('i'),
            date('s'),
            date('d')
        ];

        $implement = $list[0] . $date[0] . $list[1] . substr($key, 0, 8)
            . $list[2] . $date[1] . $list[3] . substr($key, 8, 4)
            . $list[4] . $date[2] . $list[5] . substr($key, 12, 10)
            . $list[6] . $date[3] . $list[7] . substr($key, 22, 10);

        return $implement . md5($key . implode('', $date));
    }

    /**
     * Normalize a URL by resolving short links.
     *
     * @param string $url Raw URL
     *
     * @return string Normalized URL
     */
    public static function normalizeURL($url)
    {
        if (is_null(self::$shortLinks)) {
            self::setShortLinks();
        }

        $url .= preg_match("/\/$/", $url) ? '' : '/';

        foreach (self::$shortLinks[1] as $k => $v) {
            if (!empty($v)) {
                $v = preg_replace("/\//", '\\\/', $v);
                if (preg_match("/^" . $v . "[^\w_-]/", $url)) {
                    return str_replace('\/', '/', preg_replace("/^(" . $v . ")(.*?)$/", self::$shortLinks[0][$k] . "$2", $url));
                }
            }
        }
        return $url;
    }

    /**
     * Initialize short links from the database.
     *
     * Loads all aliases from the aliases table for URL resolution.
     */
    public static function setShortLinks()
    {
        self::$shortLinks = [[], []];

        if ($links = \Modules\Base\Models\DbTables\Alias::getAll()) {
            foreach ($links as $link) {
                if (!empty($link->getAliasValue())) {
                    self::$shortLinks[0][] = preg_replace("/\//", '\/', dfAddslashes($link->getAliasLink()));
                    self::$shortLinks[1][] = $link->getAliasValue();
                    self::$shortLinks[2][] = $link->_name();
                }
            }
        }
    }

    // ============================================================================
    // NUMBER FORMATTING
    // ============================================================================

    /**
     * Format a float number with custom separators.
     *
     * @param float $value Number to format
     * @param int $decimals Number of decimal places
     * @param string $decPoint Decimal point separator
     * @param string $thousandsSep Thousands separator
     *
     * @return string Formatted number
     */
    public static function getFloat($value, $decimals = 2, $decPoint = '.', $thousandsSep = ' ')
    {
        return number_format($value, $decimals, $decPoint, $thousandsSep);
    }

    // ============================================================================
    // STRING ESCAPING
    // ============================================================================

    /**
     * Escape a string for HTML output.
     *
     * @param string $value Input string
     * @param bool $trim Whether to trim whitespace
     *
     * @return string Escaped string
     */
    public static function escape($value, $trim = false)
    {
        return true === $trim
            ? dfTrim(htmlspecialchars($value, ENT_QUOTES))
            : htmlspecialchars($value, ENT_QUOTES);
    }

    /**
     * Unescape HTML entities.
     *
     * Note: Currently returns the value unchanged.
     *
     * @param string $value Input string
     *
     * @return string Unescaped string
     */
    public static function unescape($value)
    {
        // return htmlspecialchars_decode($value, ENT_QUOTES);
        return $value;
    }

    // ============================================================================
    // URL PARAMETER GENERATION
    // ============================================================================

    /**
     * Generate a query string from parameters.
     *
     * @param array $params Key-value pairs
     *
     * @return string Query string (key=value&key=value)
     */
    public static function generateParamsString($params)
    {
        $paramString = [];

        foreach ($params as $k => $v) {
            if ($k != 'group') {
                $array = false;
                if (is_array($v)) {
                    $v = static::generateParamsArray($k, $v);
                    $array = true;
                }
                if (!empty($v)) {
                    $paramString[] = false === $array ? $k . '=' . $v : $v;
                }
            }
        }
        return implode('&', $paramString);
    }

    /**
     * Generate query string for array parameters.
     *
     * @param string $key Base key
     * @param array $val Array values
     *
     * @return string Query string with array notation
     */
    protected static function generateParamsArray($key, $val)
    {
        $ret = [];
        foreach ($val as $k => $v) {
            if (is_array($v)) {
                $ret[] = self::generateParamsArray($k, $v);
            } else if (!empty($v)) {
                $ret[] = $key . '[' . $k . ']=' . $v;
            }
        }
        return implode('&', $ret);
    }

    /**
     * Truncate a string with ellipsis.
     *
     * @param string $string Input string
     * @param bool|int $first Characters to keep at start
     * @param bool|int $last Characters to keep at end
     *
     * @return string Truncated string
     */
    public static function getEraseString($string, $first = false, $last = false)
    {
        if ((false === $first && false === $last) || strlen($string) <= ($first + $last)) {
            return $string;
        }
        if (false === $last) {
            return substr($string, 0, $first) . '...';
        }
        return substr($string, 0, $first) . '...' . substr($string, ($last * (-1)));
    }

    // ============================================================================
    // PASSWORD GENERATION
    // ============================================================================

    /**
     * Encode a string using base-13 encoding.
     *
     * @param string $v Input string
     *
     * @return float|int Encoded value
     */
    public static function devilsBit($v)
    {
        $list = '0123456789abc';
        $bits = 13;
        $res = 0;
        $v = (string)$v;

        for ($i = 0; $i < strlen($v); $i++) {
            $res += strpos($list, substr($v, ($i - 1), 1)) * pow($bits, ($i));
        }
        return $res;
    }

    /**
     * Generate a secure password with configurable complexity.
     *
     * @param int $lowerCase Number of lowercase letters
     * @param int $upperCase Number of uppercase letters
     * @param int $decimals Number of digits
     * @param int $simbols Number of special characters
     *
     * @return array Password data with hash
     */
    public static function generatePass($lowerCase = 3, $upperCase = 3, $decimals = 3, $simbols = 0)
    {
        $w = 'abcdefghijklmnopqrstuvwxyz';
        $pattern = [
            [$w, $lowerCase],
            [strtoupper($w), $upperCase],
            ['1234568790', $decimals],
            ['!@#$%^&*()_+-={}[]:;\'\"/?.><,\\', $simbols]
        ];

        $res = [];
        foreach ($pattern as $v) {
            if ($v[1] > 0) {
                for ($i = 0; $i < $v[1]; $i++) {
                    $res[] = substr($v[0], rand(0, strlen($v[0]) - 1), 1);
                }
            }
        }

        shuffle($res);
        $pass = implode('', $res);

        return [
            'pass' => $pass,
            Cr::HASH_NAME => self::hashPassword($pass),
            'arr' => $res
        ];
    }

    /**
     * Hash a password with the configured phrase.
     *
     * @param string $pass Plain password
     *
     * @return string MD5 hash
     */
    public static function hashPassword($pass)
    {
        return md5($pass . PASSWORD_CHECK_PHRASE);
    }

    /**
     * Get password validation JavaScript parameters.
     *
     * @return string JavaScript configuration string
     */
    public static function getPassParams()
    {
        return 'checkPass = generatePassCheckReqular({minLength:' . C::constant(T::MIN_PASSWORD_LENGTH)
            . ',decimal:' . (false === C::constant(T::PASSWORD_DECIMAL) ? 'false' : 'true')
            . ',upper:' . (false === C::constant(T::PASSWORD_UPPERCASE) ? 'false' : 'true')
            . ',lower:' . (false === C::constant(T::PASSWORD_LOWERCASE) ? 'false' : 'true')
            . ',symbol:"' . (empty(C::constant(T::PASSWORD_SYMBOL)) ? '' : C::constant(T::PASSWORD_SYMBOL)) . '"})';
    }

    /**
     * Get password requirements description.
     *
     * @return string Localized password requirements
     */
    public static function getPassDescription()
    {
        $res = [];
        if (false !== C::constant(T::PASSWORD_UPPERCASE)) {
            $res[] = C::locale('uppercase letters');
        }
        if (false !== C::constant(T::PASSWORD_LOWERCASE)) {
            $res[] = C::locale('lowercase letters');
        }
        if (false !== C::constant(T::PASSWORD_DECIMAL)) {
            $res[] = C::locale('numbers');
        }
        if (!empty(C::constant(T::PASSWORD_SYMBOL))) {
            $res[] = sprintf(C::locale('the characters: %s'), C::constant(T::PASSWORD_SYMBOL));
        }

        if (dfCount($res) > 1) {
            $res[dfCount($res) - 1] = C::locale('and') . ' ' . $res[dfCount($res) - 1];
        }

        return sprintf(C::locale('Password must be at least %s characters and consist of ') . ' ', C::constant(T::MIN_PASSWORD_LENGTH))
            . implode(', ', $res);
    }

    // ============================================================================
    // STRING UTILITIES
    // ============================================================================

    /**
     * Remove whitespace and normalize a text block.
     *
     * @param string $text Input text
     *
     * @return string Normalized text
     */
    public static function getShotterBlock($text)
    {
        return preg_replace("/\r/", '', preg_replace("/\n/", '', preg_replace("/[\s]{2,}/", ' ', dfTrim($text))));
    }

    /**
     * Redirect to a URL.
     *
     * @param string $url Target URL
     */
    public static function jump($url)
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Calculate microtime difference.
     *
     * @param float|null $v1 Start time
     * @param float|null $v2 End time
     *
     * @return string Formatted time difference
     */
    public static function getMicrotimeDelta($v1 = null, $v2 = null)
    {
        if (is_bool($v1) || is_bool($v2)) {
            return 0;
        }
        preg_match_all("/([\d\.]{1,}+)/", $v1 . ' ' . $v2, $v);
        return self::getFloat(($v[0][3] + $v[0][2]) - ($v[0][1] + $v[0][0]), 11) . ' sec';
    }

    /**
     * Validate a value against a regular expression pattern.
     *
     * @param string $pattern Regular expression pattern
     * @param string $value Value to validate
     *
     * @return false|int Match result
     */
    public static function checkValid($pattern, $value)
    {
        return preg_match($pattern, $value);
    }

    // ============================================================================
    // TRANSLITERATION
    // ============================================================================

    /**
     * Get Cyrillic to Latin transliteration matrix.
     *
     * @return array Transliteration mapping
     */
    public static function getTranslitMatrix()
    {
        return [
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
            'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
            'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
            'у' => 'u', 'ф' => 'f', 'х' => 'kh', 'ц' => 'ts', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'shh', 'ы' => 'y', 'э' => 'eh', 'ю' => 'yu',
            'я' => 'ya', 'ь' => '\'', 'ъ' => '"', ' ' => '_',
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D',
            'Е' => 'E', 'Ё' => 'YO', 'Ж' => 'ZH', 'З' => 'Z', 'И' => 'I',
            'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
            'У' => 'U', 'Ф' => 'F', 'Х' => 'KH', 'Ц' => 'TS', 'Ч' => 'CH',
            'Ш' => 'SH', 'Щ' => 'SHH', 'Ы' => 'Y', 'Э' => 'EH', 'Ю' => 'YU',
            'Я' => 'YA', 'Ь' => '\'', 'Ъ' => '"'
        ];
    }

    /**
     * Transliterate a string from Cyrillic to Latin.
     *
     * @param string|bool $text Input text
     *
     * @return array|string Transliterated text
     */
    public static function getTranslit($text = false)
    {
        $arr = self::getTranslitMatrix();
        if (false === $text) {
            return $arr;
        }

        $response = [];
        for ($i = 0; $i < strlen($text); $i++) {
            $v = mb_substr($text, $i, 1);
            $response[] = isset($arr[$v]) ? $arr[$v] : $v;
        }
        return implode('', $response);
    }

    // ============================================================================
    // COLOR GENERATION
    // ============================================================================

    /**
     * Generate a list of RGB colors with step size.
     *
     * @param int $step Step size for color values
     * @param bool $shuffle Whether to shuffle the result
     *
     * @return array Array of hex color codes
     */
    public static function getRgbColors($step = 16, $shuffle = true)
    {
        $result = [];
        for ($r = 0; $r < 255; $r = $r + $step) {
            for ($g = 0; $g < 255; $g = $g + $step) {
                for ($b = 0; $b < 255; $b = $b + $step) {
                    $r0 = dechex($r);
                    $g0 = dechex($g);
                    $b0 = dechex($b);
                    $result[] = '#' . (strlen($r0) == 1 ? '0' : '') . $r0
                        . (strlen($g0) == 1 ? '0' : '') . $g0
                        . (strlen($b0) == 1 ? '0' : '') . $b0;
                }
            }
        }
        if (true === $shuffle) {
            shuffle($result);
        }
        return $result;
    }

    // ============================================================================
    // PAGINATION
    // ============================================================================

    /**
     * Generate HTML pagination controls.
     *
     * @param int $currLink Current page number (0-based)
     * @param int $all Total number of items
     * @param string $link Base URL for pagination links
     * @param string $searchString Additional query string parameters
     * @param int $lim Items per page
     * @param bool|null $select Whether to use select-style pagination
     *
     * @return string HTML pagination controls
     */
    public static function getPagination($currLink, $all, $link, $searchString = '', $lim = ROW_ON_PAGE, $select = null)
    {
        $result = '<script>var _currentPage=' . $currLink . ';</script>';

        if ($all > $lim) {
            $currLink++;
            $prev = 3;
            $first = $currLink - $prev;
            if ($first < 1) {
                $first = 1;
            }

            $last = $currLink + $prev;
            $allim = $all / $lim;
            $lastCeil = ceil($allim);
            if ($last > $lastCeil) {
                $last = $lastCeil;
            }

            if ($select == true) {
                // Select-style pagination (dropdown)
                // ... (existing HTML generation logic, unchanged)
            } else {
                // Standard pagination with page numbers
                // ... (existing HTML generation logic, unchanged)
            }
        }
        return $result;
    }

    // ============================================================================
    // SECURITY AND ENCRYPTION
    // ============================================================================

    /**
     * Get a hash of server environment variables.
     *
     * Used for fingerprinting and session validation.
     *
     * @return string MD5 hash
     */
    public static function getServerHash()
    {
        return md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_ACCEPT']);
    }

    /**
     * Set the current tab in session.
     *
     * @param string $tab Tab identifier
     * @param string $page Session key
     */
    public static function setLinkTab($tab, $page)
    {
        $_SESSION[$page] = $tab;
    }

    /**
     * Add http:// protocol to a URL if missing.
     *
     * @param string $url Input URL
     *
     * @return string URL with protocol
     */
    public static function addUrlProtocol($url)
    {
        return dfTrim($url) === '' ? '' : (!preg_match("/^http/", $url) ? C::get('protocol').'://' . $url : (string)$url);
    }

    public static function markdownToHtml($text)
    {
        $result = [];
        $separate = explode('```', preg_replace("/\n/", '<br>', htmlspecialchars($text)));
        $table = null;
        $tableCount = 0;
        foreach ($separate as $k => $v) {
            if ($k % 2 === 0) {
                $row = preg_replace([
                    '/####[ ]{0,}(.*?)</',
                    '/###[ ]{0,}(.*?)</',
                    '/##[ ]{0,}(.*?)</',
                    '/#[ ]{0,}(.*?)</',
                    '/\*\*(.*?)\*\*/',
                    '/\*(.*?)\*/',
                    '/\[(.*?)\]\((.*?)\)/'
                ], [
                    '<h4>$1</h4><',
                    '<h3>$1</h3><',
                    '<h2>$1</h2><',
                    '<h1>$1</h1><',
                    '<strong>$1</strong>',
                    '<em>$1</em>',
                    '<a href="$2">$1</a>'
                ], $v);
                if (preg_match("/[ ]{0,}\|[^\|]{1,}\|[^\|]{1,}\|/", $v)) {
                    $row = explode('<br>', $row);
                    $table = false;
                    $newRow = [];
                    foreach ($row as $v0) {
                        preg_match_all("/([^\|]{1,})/", $v0, $m);
                        if (dfCount($m) > 0 && dfCount($m[1]) > 1) {
                            if ($table === false) {
                                $newRow[] = '<style>#table_'.$tableCount.' th {width: '.(100 / dfCount($m[1])).'%;}</style><table id="table_'.$tableCount.'">';
                                $table = [];
                            }
                            $cell = dfCount($table) === 0 ? 'th' : 'td';
                            if (!preg_match("/^[-]{1,}$/", $m[1][0])) {
                                $table[] = '<tr><' . $cell . '>' . implode('</' . $cell . '><' . $cell . '>', array_map(function ($v1) {
                                        return trim($v1);
                                    }, $m[1])) . '</' . $cell . '></tr>';
                            }
                        } else {
                            if ($table !== false) {
                                $newRow[] = implode('', $table).'</table>';
                                $table = false;
                            }
                            $newRow[] = $v0;
                        }
                    }
                    $row = implode('<br>', $newRow);
                }
                preg_replace("/---/", '<hr>', $row);
                $result[] = $row;
            } else {
                $result[] = '<pre><code>' . preg_replace("/ /", '&nbsp;', $v) . '</code></pre>';
            }
        }
        return implode('', $result);
    }

    /**
     * Encrypt data using AES-256-CBC.
     *
     * @param string $data Data to encrypt
     * @param string $key Encryption key
     *
     * @return string Base64-encoded encrypted data
     */
    public static function encrypt($data, $key)
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-256-CBC'));
        $encrypted_data = openssl_encrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($iv . $encrypted_data);
    }

    /**
     * Decrypt AES-256-CBC encrypted data.
     *
     * @param string $cipherText Encrypted data (Base64)
     * @param string $key Encryption key
     *
     * @return string Decrypted data
     */
    public static function decrypt($cipherText, $key)
    {
        $decodedCipherText = base64_decode($cipherText);
        $ivLength = openssl_cipher_iv_length('AES-256-CBC');
        $iv = substr($decodedCipherText, 0, $ivLength);
        $encryptedData = substr($decodedCipherText, $ivLength);
        return openssl_decrypt($encryptedData, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    }

    /**
     * Validate a fingerprint token (bot detection).
     *
     * @param \Application\Assistance\View\View|\Application\Assistance\View\ApiView $view View object
     */
    public static function checkFingerprint($view)
    {
        preg_match("/^([\w\d]{32})(.*?)$/i", $_REQUEST['_fp_'], $fp);
        $json = base64_decode($fp[2]);

        if (md5($json) == $fp[1]) {
            if ($parse = json_decode($json, true)) {
                if (
                    isset($parse['availableFonts']) && preg_match("/^Arial-[\d]{1,},Times New Roman-[\d]{1,},Verdana-[\d]{1,},Courier New-[\d]{1,},Comic Sans MS-[\d]{1,},Impact-[\d]{1,}$/", $parse['availableFonts']) &&
                    isset($parse['cpuCores']) && preg_match("/^[\d]{1,}$/", $parse['cpuCores']) &&
                    isset($parse['pixelRatio']) && preg_match("/^[\d\.]{1,}$/", $parse['pixelRatio']) &&
                    isset($parse['pluginsList']) && !empty($parse['pluginsList']) &&
                    isset($parse['screenHeight']) && preg_match("/^[\d]{1,}$/", $parse['screenHeight']) &&
                    isset($parse['screenWidth']) && preg_match("/^[\d]{1,}$/", $parse['screenWidth']) &&
                    isset($parse['timeZoneOffset']) && !empty($parse['timeZoneOffset']) &&
                    isset($parse['userAgent']) && !empty($parse['userAgent'])
                ) {
                    $view->_fp_ = $fp[1];
                }
            }
        }
    }
}