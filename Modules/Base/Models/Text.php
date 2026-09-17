<?php

namespace Modules\Base\Models;

/**
 * Text model class.
 *
 * Represents a static text content record. Stores reusable text blocks
 * that can be translated and used across the application.
 *
 * @package   Modules\Base\Models
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method int getTextId()
 * @method $this setTextId(int $text_id)
 * @method string getTextTag()
 * @method $this setTextTag(string $text_tag)
 * @method string getTextValue()
 * @method $this setTextValue(string $text_value)
 * @method string getTextDescription()
 * @method $this setTextDescription(string $text_description)
 * @method int getTextGroup()
 * @method $this setTextGroup(int $text_group)
 * @method int getTextMenu()
 * @method $this setTextMenu(int $text_menu)
 * @method int getTextMenuEnabled()
 * @method $this setTextMenuEnabled(int $text_menu_enabled)
 * @method int getTextAlternative()
 * @method $this setTextAlternative(int $text_alternative)
 * @method int getTextAlternativeEnabled()
 * @method $this setTextAlternativeEnabled(int $text_alternative_enabled)
 * @method int getProjectId()
 * @method $this setProjectId(int $project_id)
 *
 * @method \Modules\Base\Models\Project linkProjectId()
 */
class Text extends \Application\Assistance\Model
{
    /** @var int Primary key */
    public $text_id;

    /** @var string Text tag (unique identifier) */
    public $text_tag;

    /** @var string Text content */
    public $text_value;

    /** @var string Text description */
    public $text_description;

    /** @var int Text group */
    public $text_group;

    /** @var int Menu position */
    public $text_menu;

    /** @var int Menu enabled flag */
    public $text_menu_enabled;

    /** @var int Alternative text ID */
    public $text_alternative;

    /** @var int Alternative enabled flag */
    public $text_alternative_enabled;

    /** @var int Project ID */
    public $project_id;
}