<?php

namespace Modules\Base\Models;

/**
 * Tvalue model class.
 *
 * Represents a translation value record. Stores the actual translated
 * text for a specific key and language combination.
 *
 * @package   Modules\Base\Models
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method int getTvalueId()
 * @method $this setTvalueId(int $tvalue_id)
 * @method int getTkeyId()
 * @method $this setTkeyId(int $tkey_id)
 * @method int getLanguageId()
 * @method $this setLanguageId(int $language_id)
 * @method string getTvalueValue()
 * @method $this setTvalueValue(string $tvalue_value)
 *
 * @method Tkey|false linkTKeyId()
 * @method \Modules\Geo\Models\Language|false linkLanguageId()
 */
class Tvalue extends \Application\Assistance\Model
{
    /** @var int Primary key */
    public $tvalue_id;

    /** @var int Translation key ID */
    public $tkey_id;

    /** @var int Language ID */
    public $language_id;

    /** @var string Translated text value */
    public $tvalue_value;
}