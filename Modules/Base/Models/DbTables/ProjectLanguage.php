<?php

namespace Modules\Base\Models\DbTables;

/**
 * ProjectLanguage database table class.
 *
 * Manages the many-to-many relationship between projects and languages.
 * This table determines which languages are available for each project
 * and their display order in the user interface.
 *
 * Features:
 * - Project isolation via DatabaseNormalProject
 * - Language availability per project
 * - Display order control
 * - Language lookup by project
 *
 * @package   Modules\Base\Models\DbTables
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method static \Modules\Base\Models\ProjectLanguage|array|false getRow($ids = null, $page = false, $order = false, $model = true, $cache = true)
 * @method static \Modules\Base\Models\ProjectLanguage[]|array|false getAll($page = false, $order = false, $model = true)
 */
class ProjectLanguage extends \Application\Assistance\DatabaseNormalProject
{
    /** Primary key field */
    const PROJECT_LANGUAGE_ID = 'project_language_id';

    /** Language ID (FK to Language table) */
    const LANGUAGE_ID = 'language_id';

    /** Display order for languages in the project */
    const PROJECT_LANGUAGE_ORDER = 'project_language_order';

    /** Table name */
    public static $_table = 'project_languages';

    /** Primary key field */
    public static $_index = self::PROJECT_LANGUAGE_ID;

    /**
     * Field definitions.
     *
     * @var array
     */
    public static $_fields = [
        self::PROJECT_LANGUAGE_ID => [],
        self::LANGUAGE_ID => [self::FP_LINK => \Modules\Geo\Models\DbTables\Language::class]
    ];

    /**
     * Get project language assignment by language ID.
     *
     * @param int $languageId Language ID
     *
     * @return \Modules\Base\Models\ProjectLanguage|bool
     */
    public static function getByLanguage($languageId)
    {
        return (self::getSelect())
            ->addWhere(self::createWhere(self::LANGUAGE_ID, $languageId))
            ->pop()
            ->result();
    }
}