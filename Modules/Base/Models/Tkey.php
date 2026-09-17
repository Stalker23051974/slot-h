<?php

namespace Modules\Base\Models;

/**
 * Tkey model class.
 *
 * Represents a translation key record. Stores unique text strings
 * with their MD5 hashes for the localization system.
 *
 * @package   Modules\Base\Models
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method int getTkeyId()
 * @method $this setTkeyId(int $tkey_id)
 * @method string getTkeyKey()
 * @method $this setTkeyKey(string $tkey_key)
 * @method string getTkeyHash()
 * @method $this setTkeyHash(string $tkey_hash)
 *
 * @method Tvalue[]
 */
class Tkey extends \Application\Assistance\Model
{
    /** @var int Primary key */
    public $tkey_id;

    /** @var string Original text string */
    public $tkey_key;

    /** @var string MD5 hash of the text string */
    public $tkey_hash;
}