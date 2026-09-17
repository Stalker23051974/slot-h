<?php

namespace Modules\Base\Models\DbTables;

/**
 * TText database table class.
 *
 * Translation table for static texts. Stores localized versions
 * of text content for multi-language support.
 *
 * Features:
 * - Language-specific text content
 * - Parent text reference
 * - Translation management
 * - Description field for context
 * - Used by the text system for localized content
 *
 * @package   Modules\Base\Models\DbTables
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method static \Modules\Base\Models\TText|array|false getRow($ids = null, $page = false, $order = false, $model = true, $cache = true)
 * @method static \Modules\Base\Models\TText[]|array|false getAll($page = false, $order = false, $model = true)
 * @method static \Modules\Base\Models\TText[]|false getTranslate($ID)
 */
class TText extends \Application\Assistance\DatabaseNormal
{
    /** Primary key field */
    const T_TEXT_ID = 't_text_id';

    /** Language ID (FK to Language table) */
    const LANGUAGE_ID = 'language_id';

    /** Parent text ID (FK to Text table) */
    const PARENT = 'parent';

    /** Localized text content */
    const TEXT_VALUE = 'text_value';

    /** Localized description */
    const TEXT_DESCRIPTION = 'text_description';

    /** Table name */
    public static $_table = 't_texts';

    /** Primary key field */
    public static $_index = self::T_TEXT_ID;

    /**
     * Field definitions.
     *
     * @var array
     */
    public static $_fields = [
        self::T_TEXT_ID => [],
        self::LANGUAGE_ID => [self::FP_INDEX => true, self::FP_CACHE => true, self::FP_LINK => \Modules\Geo\Models\DbTables\Language::class],
        self::PARENT => [self::FP_INDEX => true, self::FP_CACHE => true, self::FP_LINK => Text::class],
        self::TEXT_VALUE => [self::FP_TYPE => self::TYPE_TEXT, self::FP_NULL => true],
        self::TEXT_DESCRIPTION => [self::FP_TYPE => self::TYPE_STRING, self::FP_NULL => true],
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