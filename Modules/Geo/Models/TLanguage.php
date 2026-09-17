<?php

namespace Modules\Geo\Models;

/**
 * TLanguage model class.
 *
 * Represents a translation record for languages. Stores localized
 * language names for multi-language support.
 *
 * @package   Modules\Geo\Models
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method int getTLanguageId()
 * @method $this setTLanguageId(int $t_language_id)
 * @method int getLanguageId()
 * @method $this setLanguageId(int $language_id)
 * @method int getParent()
 * @method $this setParent(int $parent)
 * @method string getLanguageName()
 * @method $this setLanguageName(string $language_name)
 *
 * @method Language|false linkLanguageId()
 * @method Language|false linkParent()
 */
class TLanguage extends \Application\Assistance\Model
{
    /** @var int Primary key */
    public $t_language_id;

    /** @var int Language ID */
    public $language_id;

    /** @var int Parent language ID */
    public $parent;

    /** @var string Localized language name */
    public $language_name;
}