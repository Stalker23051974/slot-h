<?php

namespace Modules\Base\Models\DbTables;

/**
 * Icon database table class.
 *
 * Manages SVG icons used throughout the system. Icons are stored as
 * SVG path data and referenced by constants for consistent usage.
 *
 * Features:
 * - SVG path storage for icons
 * - Constant-based referencing
 * - Frontend availability flag
 * - Cached for performance
 *
 * @package   Modules\Base\Models\DbTables
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method static \Modules\Base\Models\Icon|array|false getRow($ids = null, $page = false, $order = false, $model = true, $cache = true)
 * @method static \Modules\Base\Models\Icon[]|array|false getAll($page = false, $order = false, $model = true)
 */
class Icon extends \Application\Assistance\DatabaseNormal
{
    /** Primary key field */
    const ICON_ID = 'icon_id';

    /** SVG path data (JSON array of path strings) */
    const ICON_PATH = 'icon_path';

    /** Icon constant identifier (used in code) */
    const ICON_CONSTANT = 'icon_constant';

    /** Whether the icon is available on the frontend */
    const ICON_FRONTEND = 'icon_frontend';

    /** Table name */
    public static $_table = 'icons';

    /** Primary key field */
    public static $_index = self::ICON_ID;

    /** Flag value for frontend availability */
    const FRONTEND_USE = 1;

    /**
     * Field definitions.
     *
     * @var array
     */
    public static $_fields = [
        self::ICON_ID => [self::FP_INDEX => true],
        self::ICON_PATH => [self::FP_TYPE => self::TYPE_TEXT],
        self::ICON_CONSTANT => [],
        self::ICON_FRONTEND => [self::FP_NULL => true],
    ];
}