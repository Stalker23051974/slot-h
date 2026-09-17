<?php

namespace Modules\Base\Models\DbTables;

/**
 * Text database table class.
 *
 * Manages static text content for the system. Stores reusable text blocks
 * that can be translated and used across the application.
 *
 * Features:
 * - Project isolation via DatabaseNormalProject
 * - Translation support for text content
 * - Tag-based text retrieval
 * - Menu integration
 * - Alternative text support (A/B testing)
 * - Form association
 * - Grouping for organization
 *
 * @package   Modules\Base\Models\DbTables
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method static \Modules\Base\Models\Text|array|false getRow($ids = null, $page = false, $order = false, $model = true, $cache = true)
 */
class Text extends \Application\Assistance\DatabaseNormalProject
{
    /** Primary key field */
    const TEXT_ID = 'text_id';

    /** Text tag (unique identifier) */
    const TEXT_TAG = 'text_tag';

    /** Text content */
    const TEXT_VALUE = 'text_value';

    /** Text description */
    const TEXT_DESCRIPTION = 'text_description';

    /** Text group */
    const TEXT_GROUP = 'text_group';

    /** Menu position */
    const TEXT_MENU = 'text_menu';

    /** Menu enabled flag */
    const TEXT_MENU_ENABLED = 'text_menu_enabled';

    /** Alternative text ID (for A/B testing) */
    const TEXT_ALTERNATIVE = 'text_alternative';

    /** Alternative enabled flag */
    const TEXT_ALTERNATIVE_ENABLED = 'text_alternative_enabled';

    /** Table name */
    public static $_table = 'texts';

    /** Primary key field */
    public static $_index = self::TEXT_ID;

    /** Enable translation for text content */
    public static $_translate = true;

    /** Name field for getByName() lookups */
    public static $_name = self::TEXT_DESCRIPTION;

    /**
     * Field definitions.
     *
     * @var array
     */
    public static $_fields = [
        self::TEXT_ID => [],
        self::TEXT_TAG => [self::FP_TYPE => self::TYPE_STRING, self::FP_INDEX => true, self::FP_CACHE => true],
        self::TEXT_VALUE => [self::FP_TYPE => self::TYPE_TEXT],
        self::TEXT_DESCRIPTION => [self::FP_TYPE => self::TYPE_STRING, self::FP_LENGTH => 255],
        self::TEXT_GROUP => [self::FP_NULL => true],
        self::TEXT_MENU => [self::FP_NULL => true],
        self::TEXT_MENU_ENABLED => [self::FP_NULL => true],
        self::TEXT_ALTERNATIVE => [self::FP_NULL => true],
        self::TEXT_ALTERNATIVE_ENABLED => [self::FP_NULL => true],
    ];

    /** Privacy policy tag */
    const TEXT_TAG_PRIVACY_POLICY = 'privacy';

    /** Terms of use tag */
    const TEXT_TAG_TERN_OF_USE = 'terms';

    /**
     * Get all texts (overrides parent).
     *
     * @param bool|int   $page  Page number
     * @param bool|array $order Order by clause
     * @param bool       $model Return Model objects
     *
     * @return \Modules\Base\Models\Text[]|array|false
     */
    public static function getAll($page = false, $order = false, $model = true)
    {
        (static::getSelect())->addWhere([self::PROJECT_ID => CURRENT_PROJECT]);
        static::setModelResponse($model);
        static::$_select->_page = $page;
        static::$_select->_order = $order;
        $result = static::select();
        return is_bool($result) ? false : $result;
    }

    /**
     * Get text by tag.
     *
     * @param string $tag   Text tag
     * @param bool   $model Return model or string
     *
     * @return \Modules\Base\Models\Text|string
     */
    public static function getTextByTag($tag, $model = false)
    {
        if (!$result = (static::getSelect())
            ->addWhere(static::createWhere(self::TEXT_TAG, $tag))
            ->result()) {
            return '';
        }

        $result = array_pop($result);

        if (true === $model) {
            return $result;
        }

        return self::getTranslateText($result);
    }

    /**
     * Get translated text from a text model.
     *
     * @param \Modules\Base\Models\Text $result Text model
     *
     * @return string Translated text or original if translation not found
     */
    public static function getTranslateText($result)
    {
        $res = $result->getTranslate();
        /** @var \Modules\Base\Models\TText[] $res */

        if (!isset($res[\Application\Translate::getLanguage()]) || empty($res[\Application\Translate::getLanguage()]->getTextValue())) {
            return $result->getTextValue();
        }

        $res = $res[\Application\Translate::getLanguage()];
        /** @var \Modules\Base\Models\TText $res */
        return $res->getTextValue();
    }
}