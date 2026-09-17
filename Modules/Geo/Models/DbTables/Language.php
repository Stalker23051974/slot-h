<?php

namespace Modules\Geo\Models\DbTables;

use Application\Assistance\Select\FieldHandler;
use Application\Assistance\Select\JoinField;
use Application\Assistance\Select\Where;
use \Modules\Base\Models\DbTables\ProjectLanguage as PL;

/**
 * Language database table class.
 *
 * Manages language records for multi-language support. Stores
 * language metadata including ISO codes, status, voice settings,
 * and RTL/LTR direction.
 *
 * Features:
 * - Language status (enabled/disabled)
 * - ISO 1 and ISO 3 codes
 * - Voice settings for text-to-speech
 * - RTL/LTR text direction
 * - Active language filtering by project
 *
 * @package   Modules\Geo\Models\DbTables
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method static \Modules\Geo\Models\Language|\Modules\Geo\Models\Language[]|array|false getRow($ids = null, $page = false, $order = false, $model = true, $cache = true)
 * @method static \Modules\Geo\Models\Language[]|array|false getAll($page = false, $order = false, $model = true)
 */
class Language extends \Application\Assistance\DatabaseExtend
{
    /** Primary key field */
    const LANGUAGE_ID = 'language_id';

    /** Language name */
    const LANGUAGE_NAME = 'language_name';

    /** ISO 1 code (2 letters) */
    const LANGUAGE_ISO_1 = 'language_iso_1';

    /** ISO 3 code (3 letters) */
    const LANGUAGE_ISO_3 = 'language_iso_3';

    /** Language status */
    const LANGUAGE_STATUS = 'language_status';

    /** Privacy policy URL */
    const LANGUAGE_PRIVACY_URL = 'language_privacy_url';

    /** License URL */
    const LANGUAGE_LICENSE_URL = 'language_license_url';

    /** Flag icon */
    const LANGUAGE_FLAG = 'language_flag';

    /** Text direction (LTR/RTL) */
    const LANGUAGE_DIRECTION = 'language_direction';

    /** Male voice ID */
    const LANGUAGE_VOICE_MALE = 'language_voice_male';

    /** Female voice ID */
    const LANGUAGE_VOICE_FEMALE = 'language_voice_female';

    /** Language suffix */
    const LANGUAGE_S = 'language_s';

    /** Table name */
    public static $_table = 'languages';

    /** Primary key field */
    public static $_index = self::LANGUAGE_ID;

    /** Database name */
    public static $_base = 'geo';

    /** Enable translation */
    public static $_translate = true;

    /** Name field */
    public static $_name = self::LANGUAGE_NAME;

    // ============================================================================
    // LANGUAGE ID CONSTANTS
    // ============================================================================

    /** Russian language ID */
    const LANGUAGE_RUSSIAN = 1;

    /** English language ID */
    const LANGUAGE_ENGLISH = 2;

    /** French language ID */
    const LANGUAGE_FRENCH = 3;

    /** Spanish language ID */
    const LANGUAGE_SPANISH = 4;

    /** Italian language ID */
    const LANGUAGE_ITALIAN = 5;

    /** German language ID */
    const LANGUAGE_GERMANY = 6;

    // ============================================================================
    // LANGUAGE STATUS CONSTANTS
    // ============================================================================

    /** Enabled */
    const LANGUAGE_STATUS_ENABLED = 1;

    /** Disabled */
    const LANGUAGE_STATUS_DISABLED = 2;

    /** RTL direction flag */
    const LANGUAGE_DIRECTION_RTL = 1;

    /**
     * Get voice IDs for each language.
     *
     * @return array Language ID => [male_voice_id, female_voice_id]
     */
    public static function getVoices()
    {
        return [
            self::LANGUAGE_RUSSIAN => [V::MALE => 53, V::FEMALE => 52],
            self::LANGUAGE_ENGLISH => [V::MALE => 1, V::FEMALE => 0],
            self::LANGUAGE_FRENCH => [V::MALE => 28, V::FEMALE => 27],
            self::LANGUAGE_SPANISH => [V::MALE => 57, V::FEMALE => 56],
            self::LANGUAGE_ITALIAN => [V::MALE => 38, V::FEMALE => 37],
            self::LANGUAGE_GERMANY => [V::MALE => 22, V::FEMALE => 21]
        ];
    }

    /**
     * Field definitions.
     *
     * @var array
     */
    public static $_fields = [
        self::LANGUAGE_ID => [],
        self::LANGUAGE_NAME => [self::FP_TYPE => self::TYPE_STRING, self::FP_LENGTH => 32],
        self::LANGUAGE_ISO_1 => [self::FP_TYPE => self::TYPE_STRING, self::FP_NULL => true, self::FP_LENGTH => 2],
        self::LANGUAGE_ISO_3 => [self::FP_TYPE => self::TYPE_STRING, self::FP_NULL => true, self::FP_LENGTH => 7],
        self::LANGUAGE_STATUS => [self::FP_NULL => true],
        self::LANGUAGE_PRIVACY_URL => [self::FP_TYPE => self::TYPE_STRING, self::FP_LENGTH => 255, self::FP_NULL => true],
        self::LANGUAGE_LICENSE_URL => [self::FP_TYPE => self::TYPE_STRING, self::FP_LENGTH => 255, self::FP_NULL => true],
        self::LANGUAGE_FLAG => [self::FP_TYPE => self::TYPE_STRING, self::FP_LENGTH => 3],
        self::LANGUAGE_DIRECTION => [self::FP_NULL => true],
        self::LANGUAGE_VOICE_MALE => [self::FP_NULL => true],
        self::LANGUAGE_VOICE_FEMALE => [self::FP_NULL => true],
        self::LANGUAGE_S => [self::FP_NULL => true, self::FP_TYPE => self::TYPE_STRING],
    ];

    /**
     * List template for languages.
     *
     * @var array
     */
    protected static $_listTemplate = [
        self::LIST_TEMPLATE_BASE => ['id' => self::LANGUAGE_ID, 'value' => self::LANGUAGE_NAME]
    ];

    /**
     * Get active languages for the current project.
     *
     * @return \Modules\Geo\Models\Language[]|false
     */
    public static function getActive()
    {
        return (static::getSelect())
            ->addWhere(
                static::createWhere(
                    (new JoinField())->setBase(self::getBase())->setTable(self::$_table)->setField(self::LANGUAGE_ID),
                    null,
                    Where::NOT_NULL,
                    [PL::getBase(), PL::$_table],
                    [
                        static::createWhere(
                            (new JoinField())->setBase(PL::getBase())->setTable(PL::$_table)->setField(PL::LANGUAGE_ID),
                            (new JoinField())->setBase(self::getBase())->setTable(self::$_table)->setField(self::LANGUAGE_ID)
                        ),
                        static::createWhere(
                            (new JoinField())->setBase(PL::getBase())->setTable(PL::$_table)->setField(PL::PROJECT_ID), CURRENT_PROJECT)
                    ],
                    JoinField::JOIN
                ))->order([
                (new FieldHandler())->setBase(PL::getBase())->setTable(PL::$_table)->setField(PL::PROJECT_LANGUAGE_ORDER)->setIfNull('9999')
        ])->result();
    }

    /**
     * Get a language by ISO 1 code.
     *
     * @param string $iso1 ISO 1 code (2 letters)
     *
     * @return \Modules\Geo\Models\Language|false
     */
    public static function getByIso1($iso1)
    {
        return (static::getSelect())
            ->addWhere(static::createWhere(self::LANGUAGE_ISO_1, $iso1))
            ->pop()
            ->result();
    }
}