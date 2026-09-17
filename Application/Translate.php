<?php

/**
 * Translation system for user interface strings.
 *
 * This class provides localization support by translating text strings
 * from source language to the user's selected language.
 *
 * Translation data is stored in the database:
 * - tkeys: Stores unique hashes of source texts
 * - tvalues: Stores translations for each language
 *
 * The system caches translations in memory for the current request
 * to minimize database queries.
 *
 * @package   Application
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application;

use \Modules\Base\Models\DbTables as db;

class Translate
{
    /** @var array|null Cached translations for the current request */
    public static $_cache = null;

    /** @var int|null Current language ID for the user/session */
    public static $_languageId = null;

    /** @var mixed Text direction (LTR/RTL) - reserved for future use */
    public static $_direction = null;

    /**
     * Retrieve a translated string.
     *
     * Attempts to find a translation for the given source text
     * in the user's current language.
     *
     * The process:
     * 1. Calculate MD5 hash of the source text
     * 2. Check if cache exists and populate if not
     * 3. Look up the translation by hash in the cache
     * 4. Return translation if found, otherwise return original text
     *
     * This method is called statically from templates and code:
     * CC::locale('Hello, world!')
     *
     * @param string $text The source text to translate (in default language)
     *
     * @return string Translated text, or original if translation not found
     */
    public static function get($text)
    {
        $hash = md5($text);

        // ====================================================================
        // CACHE INITIALIZATION
        // ====================================================================

        /**
         * Build translation cache on first call.
         * Loads all translations for the current language into memory.
         */
        if (is_null(self::$_cache)) {
            self::$_cache = [];

            /**
             * Get all translation values for the current language.
             */
            if ($res = db\Tvalue::getByLanguage(self::getLanguage())) {
                /**
                 * Get all translation keys to map hashes to values.
                 * Keys contain the original text and its MD5 hash.
                 */
                if ($keys = db\Tkey::getAll(false, false, false)) {
                    /**
                     * Build cache array: hash => translated_text
                     */
                    foreach ($res as $v) {
                        self::$_cache[$keys[$v[db\Tvalue::TKEY_ID]][db\Tkey::TKEY_HASH]] = $v[db\Tvalue::TVALUE_VALUE];
                    }
                }
            }
        }

        /**
         * Return cached translation if available and not empty,
         * otherwise return the original text.
         */
        return isset(self::$_cache[$hash]) && !empty(self::$_cache[$hash])
            ? self::$_cache[$hash]
            : $text;
    }

    /**
     * Determine the current user's language ID.
     *
     * Language resolution priority:
     * 1. Explicitly set language ID (for admin/forced scenarios)
     * 2. DEFAULT_LANGUAGE constant (if defined)
     * 3. Language cookie (set by user selection)
     * 4. Core default language from project configuration
     *
     * @return int|bool|null Language ID, or false/null if not determinable
     */
    public static function getLanguage()
    {
        /**
         * Return cached language ID if already resolved.
         */
        if (!is_null(self::$_languageId)) {
            return self::$_languageId;
        }

        /**
         * Resolve language ID using priority chain.
         */
        self::$_languageId = defined('DEFAULT_LANGUAGE')
            ? DEFAULT_LANGUAGE
            : (isset($_COOKIE[\Application\Assistance\Controller\Controller::FREE_LANGUAGE_COOKIE])
                ? $_COOKIE[\Application\Assistance\Controller\Controller::FREE_LANGUAGE_COOKIE]
                : \Config\CC::get('core_default_language'));

        return self::$_languageId;
    }
}