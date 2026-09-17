<?php

namespace Modules\Base\Models\DbTables;

/**
 * PersonProtected database table class.
 *
 * Manages protected user data stored as JSON. This table stores
 * user preferences and settings that don't require indexing or
 * individual querying.
 *
 * Features:
 * - JSON-based data storage
 * - Flexible field structure (no schema changes needed)
 * - User preference management
 * - Settings storage (skin, voice, translate mode, etc.)
 * - Registration data
 * - Device tokens for push notifications
 *
 * @package   Modules\Base\Models\DbTables
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method static \Modules\Base\Models\PersonProtected|array|false getRow($ids = null, $page = false, $order = false, $model = true, $cache = true)
 * @method static \Modules\Base\Models\PersonProtected[]|array|false getAll($page = false, $order = false, $model = true)
 */
class PersonProtected extends \Application\Assistance\DatabaseNormal
{
    /** Primary key field */
    const PERSON_PROTECTED_ID = 'person_protected_id';

    /** Person ID (FK to Person table) */
    const PERSON_ID = 'person_id';

    /** Protected data (JSON object) */
    const PERSON_PROTECTED_DATA = 'person_protected_data';

    /** Table name */
    public static $_table = 'person_protecteds';

    /** Primary key field */
    public static $_index = self::PERSON_PROTECTED_ID;

    // ============================================================================
    // DATA FIELD KEYS
    // ============================================================================

    /** Password field key */
    const PERSON_PASSWORD = 'password';

    /** Skin/theme preference */
    const SKIN = 'skin';

    /** Voice preference */
    const VOICE = 'voice';

    /** Authentication code */
    const AUTH_CODE = 'auth_code';

    /**
     * Field definitions.
     *
     * @var array
     */
    public static $_fields = [
        self::PERSON_PROTECTED_ID => [],
        self::PERSON_ID => [
            self::FP_INDEX => true,
            self::FP_LINK => Person::class
        ],
        self::PERSON_PROTECTED_DATA => [self::FP_TYPE => self::TYPE_TEXT],
    ];

    /**
     * Get protected data by user ID.
     *
     * @param int|array $personId Person ID(s)
     *
     * @return \Modules\Base\Models\PersonProtected[]|\Modules\Base\Models\PersonProtected|false
     */
    public static function getByUser($personId)
    {
        return (static::getSelect())
            ->addWhere(static::createWhere(self::PERSON_ID, $personId))
            ->pop(!is_array($personId))
            ->result();
    }
}