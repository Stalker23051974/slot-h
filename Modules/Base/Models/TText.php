<?php

namespace Modules\Base\Models;

/**
 * TText model class.
 *
 * Represents a translation record for static texts. Stores localized
 * versions of text content for multi-language support.
 *
 * @package   Modules\Base\Models
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method int getTTextId()
 * @method $this setTTextId(int $t_text_id)
 * @method int getLanguageId()
 * @method $this setLanguageId(int $language_id)
 * @method int getParent()
 * @method $this setParent(int $parent)
 * @method string getTextValue()
 * @method $this setTextValue(string $text_value)
 * @method string getTextDescription()
 * @method $this setTextDescription(string $text_description)
 *
 * @method \Modules\Geo\Models\Language|false linkLanguageId()
 * @method Text|false linkParent()
 */
class TText extends \Application\Assistance\Model
{
    /** @var int Primary key */
    public $t_text_id;

    /** @var int Language ID */
    public $language_id;

    /** @var int Parent text ID */
    public $parent;

    /** @var string Localized text content */
    public $text_value;

    /** @var string Localized description */
    public $text_description;
}