<?php

namespace Modules\Base\Models\DbTables;

use \Config\CC as C;
use \Application\Assistance\Controller\Controller as CC;

/**
 * Tvalue database table class.
 *
 * Translation values table for the localization system. Stores
 * the actual translated text for each language and key combination.
 *
 * Features:
 * - Language-specific translation values
 * - Key association via Tkey table
 * - Language association via Language table
 * - Bulk retrieval by key IDs
 * - Language-aware value retrieval
 * - Used by the translation system for localized content
 *
 * @package   Modules\Base\Models\DbTables
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method static \Modules\Base\Models\Tvalue|array|false getRow($ids = null, $page = false, $order = false, $model = true, $cache = true)
 * @method static \Modules\Base\Models\Tvalue[]|array|false getAll($page = false, $order = false, $model = true)
 */
class Tvalue extends \Application\Assistance\DatabaseNormal
{
    /** Primary key field */
    const TVALUE_ID = 'tvalue_id';

    /** Translation key ID (FK to Tkey table) */
    const TKEY_ID = 'tkey_id';

    /** Language ID (FK to Language table) */
    const LANGUAGE_ID = 'language_id';

    /** Translated text value */
    const TVALUE_VALUE = 'tvalue_value';

    /** Table name */
    public static $_table = 'tvalues';

    /** Primary key field */
    public static $_index = self::TVALUE_ID;

    /**
     * Field definitions.
     *
     * @var array
     */
    public static $_fields = [
        self::TVALUE_ID => [],
        self::TKEY_ID => [self::FP_INDEX => true, self::FP_LINK => Tkey::class],
        self::LANGUAGE_ID => [self::FP_INDEX => true, self::FP_CACHE => true, self::FP_LINK => \Modules\Geo\Models\DbTables\Language::class],
        self::TVALUE_VALUE => [self::FP_TYPE => self::TYPE_TEXT]
    ];

    /**
     * Get translation values by key IDs.
     *
     * @param int|array $ids Key ID(s)
     *
     * @return \Modules\Base\Models\Tvalue[]|false
     */
    public static function getByKeyIds($ids)
    {
        return (static::getSelect())
            ->addWhere(static::createWhere(self::TKEY_ID, $ids))
            ->result();
    }

    /**
     * Get translation value for a specific key and language.
     *
     * @param int      $key      Key ID
     * @param int|null $language Language ID (auto-detected if null)
     *
     * @return \Modules\Base\Models\Tvalue[]|false
     */
    public static function getValue($key, $language = null)
    {
        return (static::getSelect())
            ->addWhere([
                static::createWhere(self::TKEY_ID, $key),
                static::createWhere(self::LANGUAGE_ID, self::checkLangId($language))
            ])
            ->result();
    }

    /**
     * Get all translation values for a language.
     *
     * @param int|null $langId Language ID (auto-detected if null)
     *
     * @return \Modules\Base\Models\Tvalue[]|false
     */
    public static function getByLanguage($langId = null)
    {
        static::setModelResponse(false);
        $res = (static::getSelect())
            ->addWhere(static::createWhere(self::LANGUAGE_ID, self::checkLangId($langId)))
            ->result();
        static::setModelResponse(true);
        return $res;
    }

    /**
     * Get a valid language ID.
     *
     * If no language ID is provided, attempts to determine it from:
     * 1. DEFAULT_LANGUAGE constant
     * 2. Language cookie
     * 3. Core default language from configuration
     *
     * @param int|null $langId Language ID (optional)
     *
     * @return int Language ID
     */
    public static function checkLangId($langId = null)
    {
        if ($langId < 1) {
            if (!defined('DEFAULT_LANGUAGE')) {
                define(
                    'DEFAULT_LANGUAGE',
                    isset($_COOKIE[CC::FREE_LANGUAGE_COOKIE])
                        ? $_COOKIE[CC::FREE_LANGUAGE_COOKIE]
                        : C::get('core_default_language')
                );
            }
            $langId = DEFAULT_LANGUAGE;
        }
        return $langId;
    }
}