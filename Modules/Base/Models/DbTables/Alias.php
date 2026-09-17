<?php

namespace Modules\Base\Models\DbTables;

/**
 * Alias database table class.
 *
 * Manages short URL aliases for the system. Aliases provide human-readable
 * URLs that map to internal module/controller/action paths.
 *
 * Examples:
 * - /about -> /Free/Page/view/1
 * - /contact -> /Free/Page/view/2
 * - /faq -> /Free/Page/view/3
 *
 * Features:
 * - Project isolation via DatabaseNormalProject
 * - Translation support for alias names
 * - Visible/active alias filtering
 * - Unique alias values for URL routing
 *
 * @package   Modules\Base\Models\DbTables
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method static \Modules\Base\Models\Alias|array|false getRow($ids = null, $page = false, $order = false, $model = true, $cache = true)
 * @method static \Modules\Base\Models\Alias[]|array|false getAll($page = false, $order = false, $model = true)
 */
class Alias extends \Application\Assistance\DatabaseNormalProject
{
    /** Primary key field */
    const ALIAS_ID = 'alias_id';

    /** URL path alias (e.g., "about") */
    const ALIAS_LINK = 'alias_link';

    /** Internal target path (e.g., "Free/Page/view/1") */
    const ALIAS_VALUE = 'alias_value';

    /** Display name for the alias */
    const ALIAS_NAME = 'alias_name';

    /** Alias type (visible, hidden, etc.) */
    const ALIAS_TYPE = 'alias_type';

    /** Table name */
    public static $_table = 'aliases';

    /** Primary key field */
    public static $_index = self::ALIAS_ID;

    /** Name field for getByName() lookups */
    public static $_name = self::ALIAS_NAME;

    /**
     * Field definitions.
     *
     * @var array
     */
    public static $_fields = [
        self::ALIAS_ID => [self::FP_INDEX => true],
        self::ALIAS_LINK => [self::FP_TYPE => self::TYPE_STRING, self::FP_INDEX => true, self::FP_CACHE => true],
        self::ALIAS_VALUE => [self::FP_TYPE => self::TYPE_STRING, self::FP_NULL => true, self::FP_INDEX => true, self::FP_LENGTH => 255],
        self::ALIAS_NAME => [self::FP_TYPE => self::TYPE_STRING, self::FP_NULL => true, self::FP_INDEX => true, self::FP_LENGTH => 255],
        self::ALIAS_TYPE => [self::FP_INDEX => true, self::FP_NULL => true],
    ];

    /** Visible alias type (for active links) */
    const ALIAS_TYPE_VISIBLE = 1;

    /**
     * Get only visible/usage aliases.
     *
     * Filters aliases with:
     * - Non-empty ALIAS_VALUE (valid target)
     * - Non-empty ALIAS_NAME (display name)
     * - ALIAS_TYPE = ALIAS_TYPE_VISIBLE (active)
     *
     * @return \Modules\Base\Models\Alias[]|array|false Filtered aliases
     */
    public static function getOnlyUsage()
    {
        return (static::getSelect())
            ->addWhere([
                static::createWhere(self::ALIAS_VALUE, null, \Application\Assistance\Select\Where::NOT_NULL),
                static::createWhere(self::ALIAS_VALUE, '', \Application\Assistance\Select\Where::NOT_EQUAL),
                static::createWhere(self::ALIAS_NAME, null, \Application\Assistance\Select\Where::NOT_NULL),
                static::createWhere(self::ALIAS_NAME, '', \Application\Assistance\Select\Where::NOT_EQUAL),
                static::createWhere(self::ALIAS_TYPE, self::ALIAS_TYPE_VISIBLE)
            ])
            ->order(self::ALIAS_NAME)
            ->result();
    }
}