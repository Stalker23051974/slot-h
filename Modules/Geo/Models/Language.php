<?php

namespace Modules\Geo\Models;

/**
 * Language model class.
 *
 * Represents a language record with its metadata and settings.
 *
 * @package   Modules\Geo\Models
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method int getLanguageId()
 * @method $this setLanguageId(int $language_id)
 * @method string getLanguageName()
 * @method $this setLanguageName(string $language_name)
 * @method string getLanguageIso1()
 * @method $this setLanguageIso1(string $language_iso_1)
 * @method string getLanguageIso3()
 * @method $this setLanguageIso3(string $language_iso_3)
 * @method string getLanguageStatus()
 * @method $this setLanguageStatus(string $language_status)
 * @method string getLanguagePrivacyUrl()
 * @method $this setLanguagePrivacyUrl(string $language_privacy_url)
 * @method string getLanguageLicenseUrl()
 * @method $this setLanguageLicenseUrl(string $language_license_url)
 * @method string getLanguageFlag()
 * @method $this setLanguageFlag(string $language_flag)
 * @method string getLanguageDirection()
 * @method $this setLanguageDirection(string $language_direction)
 * @method string getLanguageVoiceMale()
 * @method $this setLanguageVoiceMale(string $language_voice_male)
 * @method string getLanguageVoiceFemale()
 * @method $this setLanguageVoiceFemale(string $language_voice_female)
 * @method string getLanguageS()
 * @method $this setLanguageS(string $language_s)
 *
 * @method array getTranslate($id = false)
 */
class Language extends \Application\Assistance\Model
{
    /** @var int Primary key */
    public $language_id;

    /** @var string Language name */
    public $language_name;

    /** @var string ISO 1 code (2 letters) */
    public $language_iso_1;

    /** @var string ISO 3 code (3 letters) */
    public $language_iso_3;

    /** @var string Language status */
    public $language_status;

    /** @var string Privacy policy URL */
    public $language_privacy_url;

    /** @var string License URL */
    public $language_license_url;

    /** @var string Flag icon */
    public $language_flag;

    /** @var string Text direction (LTR/RTL) */
    public $language_direction;

    /** @var string Male voice ID */
    public $language_voice_male;

    /** @var string Female voice ID */
    public $language_voice_female;

    /** @var string Language suffix */
    public $language_s;
}