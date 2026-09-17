<?php

namespace Modules\Geo\Models\DbTables;

/**
 * TLanguage database table class.
 *
 * Translation table for languages. Stores localized versions
 * of language names for multi-language support.
 *
 * @package   Modules\Geo\Models\DbTables
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method static \Modules\Geo\Models\TLanguage|array|false getRow($ids = null, $page = false, $order = false, $model = true, $cache = true)
 * @method static \Modules\Geo\Models\TLanguage[]|array|false getAll($page = false, $order = false, $model = true)
 * @method static \Modules\Geo\Models\TLanguage[]|false getTranslate($ID)
 */
class TLanguage extends \Application\Assistance\DatabaseNormal
{
    /** Primary key field */
    const T_LANGUAGE_ID = 't_language_id';

    /** Language ID (FK to Language table) */
    const LANGUAGE_ID = 'language_id';

    /** Parent language ID (FK to Language table) */
    const PARENT = 'parent';

    /** Localized language name */
    const LANGUAGE_NAME = 'language_name';

    /** Table name */
    public static $_table = 't_languages';

    /** Database name */
    public static $_base = 'geo';

    /** Primary key field */
    public static $_index = self::T_LANGUAGE_ID;

    /**
     * Field definitions.
     *
     * @var array
     */
    public static $_fields = [
        self::T_LANGUAGE_ID => [],
        self::LANGUAGE_ID => [self::FP_INDEX => true, self::FP_CACHE => true, self::FP_LINK => Language::class],
        self::PARENT => [self::FP_INDEX => true, self::FP_CACHE => true, self::FP_LINK => Language::class],
        self::LANGUAGE_NAME => [self::FP_TYPE => self::TYPE_STRING, self::FP_NULL => true]
    ];

    /**
     * Remove a translation record by ID.
     *
     * @param int $id Translation record ID
     */
    public static function remove($id)
    {
        (static::getSelect(\Application\Assistance\Select::DELETE))
            ->addWhere(static::createWhere(self::$_index, $id))
            ->result();
    }
}