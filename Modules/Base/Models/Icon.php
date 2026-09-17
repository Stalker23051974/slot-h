<?php

namespace Modules\Base\Models;

/**
 * Icon model class.
 *
 * Represents an SVG icon record. Stores SVG path data referenced
 * by constants for consistent icon usage across the system.
 *
 * @package   Modules\Base\Models
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method int getIconId()
 * @method $this setIconId(int $icon_id)
 * @method string getIconPath()
 * @method $this setIconPath(string $icon_path)
 * @method int getIconConstant()
 * @method $this setIconConstant(int $icon_constant)
 * @method int getIconFrontend()
 * @method $this setIconFrontend(int $icon_frontend)
 */
class Icon extends \Application\Assistance\Model
{
    /** @var int Primary key */
    public $icon_id;

    /** @var string SVG path data (JSON array of path strings) */
    public $icon_path;

    /** @var int Icon constant identifier */
    public $icon_constant;

    /** @var int Whether the icon is available on the frontend */
    public $icon_frontend;
}