<?php

namespace Modules\Geo\Models;

/**
 * Timezone model class.
 *
 * Represents a timezone record with its offset and display information.
 *
 * @package   Modules\Geo\Models
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method int getTimezoneId()
 * @method $this setTimezoneId(int $timezone_id)
 * @method string getTimezoneStandart()
 * @method $this setTimezoneStandart(string $timezone_standart)
 * @method string getTimezoneValue()
 * @method $this setTimezoneValue(string $timezone_value)
 * @method float getTimezoneOffset()
 * @method $this setTimezoneOffset(float $timezone_offset)
 * @method float getTimezoneCode()
 * @method $this setTimezoneCode(string $timezone_code)
 */
class Timezone extends \Application\Assistance\Model
{
    /** @var int Primary key */
    public $timezone_id;

    /** @var string Standard timezone name */
    public $timezone_standart;

    /** @var string Display value */
    public $timezone_value;

    /** @var float GMT offset in hours */
    public $timezone_offset;

    /** @var string Timezone code */
    public $timezone_code;

    /**
     * Get normalized timezone offset string.
     *
     * @return string Offset with + sign if positive
     */
    public function normalize()
    {
        return ($this->getTimezoneOffset() > 0 ? '+' : '') . ceil($this->getTimezoneOffset());
    }
}