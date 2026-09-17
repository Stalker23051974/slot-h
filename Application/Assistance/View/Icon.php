<?php

/**
 * SVG icon renderer for the view system.
 *
 * This class provides a fluent interface for generating SVG icons
 * from a predefined library. Icons are stored in the database as
 * SVG path data and retrieved via CC::icon().
 *
 * Features:
 * - Fluent method chaining for building icons
 * - Add CSS classes to SVG container and individual paths
 * - Add custom attributes (style, data-*, etc.)
 * - Return as string or echo directly
 * - Consistent icon set across the application
 *
 * Usage examples:
 *   $view->icon(Icon::EDIT)->addClass('text-primary')->add()
 *   $icon = $view->icon(Icon::TRASH, 'delete-btn', false)->addSvgClass('icon-lg')->add()
 *
 * @package   Application\Assistance\View
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Assistance\View;

use \Config\CC as C;

class Icon
{
    // ============================================================================
    // ICON TYPE CONSTANTS
    // ============================================================================

    /** Account multiple users icon */
    const ACCOUNT_MULTIPLE = 1;

    /** Account outline icon */
    const ACCOUNT_OUTLINE = 2;

    /** Account with star icon */
    const ACCOUNT_STAR = 3;

    /** Add icon (plus) */
    const ADD = 4;

    /** Admin panel icon */
    const ADMIN = 5;

    /** Alert/warning icon */
    const ALERT = 6;

    /** API icon */
    const API = 7;

    /** Apps grid icon */
    const APPS = 8;

    /** Arrow pointing down */
    const ARROW_DOWN = 9;

    /** Arrow pointing left */
    const ARROW_LEFT = 10;

    /** Arrow pointing right */
    const ARROW_RIGHT = 11;

    /** Arrow pointing up */
    const ARROW_UP = 12;

    /** Bank/building icon */
    const BANK = 81;

    /** Battery icon */
    const BATTERY = 13;

    /** Briefcase icon */
    const BRIEFCASE = 14;

    /** Bullhorn/megaphone icon */
    const BULLHORN = 15;

    /** Cancel/close icon */
    const CANCEL = 16;

    /** Car/vehicle icon */
    const CAR = 17;

    /** Credit card icon */
    const CARD = 18;

    /** Chart/graph icon */
    const CHART = 19;

    /** Checkmark/success icon */
    const CHECK = 20;

    /** Close/X icon */
    const CLOSE = 21;

    /** Closed folder icon */
    const CLOSED_FOLDER = 22;

    /** Cloud icon */
    const CLOUD = 23;

    /** Cog/gear icon (settings) */
    const COG = 24;

    /** Copy structure icon */
    const COPY_STRUCTURE = 25;

    /** Copy icon */
    const COPY = 26;

    /** Currency icon */
    const CURRENCY = 27;

    /** Description/text icon */
    const DESCRIPTION = 28;

    /** Developer/technical icon */
    const DEVELOPER = 29;

    /** Dislike/thumbs-down icon */
    const DISLIKE = 84;

    /** Horizontal dots (more menu) */
    const DOTS_HORIZONTAL = 31;

    /** Vertical dots (more menu) */
    const DOTS_VERTICAL = 32;

    /** Dynamic/changing icon */
    const DYNAMIC = 30;

    /** Edit/pencil icon */
    const EDIT = 33;

    /** Email/envelope icon */
    const EMAIL = 34;

    /** Eye off (hidden/visibility off) */
    const EYE_OFF = 35;

    /** Eye (visibility on) */
    const EYE = 36;

    /** Event/calendar icon */
    const EVENT = 37;

    /** File chart icon */
    const FILE_CHART = 38;

    /** Failed/error icon */
    const FAILED = 39;

    /** Filter remove/minus icon */
    const FILTER_MINUS = 40;

    /** Filter outline icon */
    const FILTER_OUTLINE = 41;

    /** Filter add/plus icon */
    const FILTER_PLUS = 42;

    /** Filter remove/clear icon */
    const FILTER_REMOVE = 43;

    /** Hint/help icon */
    const HINT = 44;

    /** History/clock icon */
    const HISTORY = 45;

    /** History toggle icon */
    const HISTORY_TOGGLE = 46;

    /** HTTP/connection icon */
    const HTTP = 47;

    /** Icon (generic) */
    const ICON = 80;

    /** Image/photo icon */
    const IMAGE = 48;

    /** Language/globe icon */
    const LANGUAGE = 49;

    /** Layout/design icon */
    const LAYOUT = 50;

    /** Lifebuoy/help icon */
    const LIFEBUOY = 51;

    /** Like/thumbs-up icon */
    const LIKE = 83;

    /** Locale/region icon */
    const LOCALE = 52;

    /** Logout/sign-out icon */
    const LOGOUT = 53;

    /** Magnifying glass (search) */
    const MAGNIFY = 54;

    /** Mail template icon */
    const MAIL_TEMPLATE = 55;

    /** Map icon */
    const MAP = 56;

    /** Map marker/pin icon */
    const MAP_MARKER = 57;

    /** Mediation/arbitration icon */
    const MEDIATION = 58;

    /** Menu down/dropdown icon */
    const MENU_DOWN = 59;

    /** Menu up icon */
    const MENU_UP = 60;

    /** Menu left icon */
    const MENU_LEFT = 61;

    /** Menu right icon */
    const MENU_RIGHT = 62;

    /** Message/chat bubble icon */
    const MESSAGE = 63;

    /** Money/currency icon */
    const MONEY = 64;

    /** Monitor/display icon */
    const MONITOR = 65;

    /** Music/audio icon */
    const MUSIC = 66;

    /** Open folder icon */
    const OPEN_FOLDER = 67;

    /** Plus/add icon */
    const PLUS = 68;

    /** Preset/template icon */
    const PRESET = 69;

    /** Refresh/reload icon */
    const REFRESH = 82;

    /** Router/network icon */
    const ROUTER = 70;

    /** RSS feed icon */
    const RSS = 71;

    /** Rule/regulation icon */
    const RULE = 72;

    /** Sandwich/menu icon */
    const SANDWICH = 73;

    /** Speedometer/dashboard icon */
    const SPEEDOMETER = 74;

    /** Storage/database icon */
    const STORAGE = 75;

    /** String/text icon */
    const STRING = 76;

    /** Text/paragraph icon */
    const TEXT = 77;

    /** Trash/delete icon */
    const TRASH = 78;

    /** Video/play icon */
    const VIDEO = 79;

    // ============================================================================
    // PROPERTIES
    // ============================================================================

    /** @var View Parent view object for context */
    private $_view;

    /** @var string|int Icon type identifier */
    private $_type;

    /** @var bool Whether to echo the icon or return as string */
    private $_echo;

    /** @var array CSS classes for individual path elements */
    private $_class = [];

    /** @var array CSS classes for the SVG container */
    private $_svgClass = [];

    /** @var array HTML attributes for path elements (style, data-*, etc.) */
    private $_attrs = [];

    /** @var array HTML attributes for the SVG container */
    private $_svgAttrs = [];

    /** @var string|null HTML ID for the SVG container */
    private $_id;

    /**
     * Create a new Icon instance.
     *
     * @param View        $view  Parent view object
     * @param string|int  $type  Icon type constant
     * @param string|null $id    Optional HTML ID for the SVG
     * @param bool        $echo  Whether to echo directly (true) or return string (false)
     */
    public function __construct($view, $type, $id = null, $echo = true)
    {
        $this->_view = $view;
        $this->_type = $type;
        $this->_echo = $echo;
        $this->_id = $id;
        return $this;
    }

    /**
     * Render the icon and output or return it.
     *
     * Retrieves the icon path data from CC::icon(), builds the SVG markup,
     * and either echoes it or returns it as a string.
     *
     * @return View|string Returns View for chaining if echo=false,
     *                     otherwise returns string output
     */
    public function add()
    {
        $icon = C::icon($this->_type);

        if (dfCount($icon) > 0) {
            // Build SVG container with classes and attributes
            $result = '<svg'
                . (null !== $this->_id ? ' id="' . $this->_id . '"' : '')
                . (dfCount($this->_svgClass) > 0 ? ' class="' . implode(' ', $this->_svgClass) . '"' : '')
                . (dfCount($this->_svgAttrs) > 0 ? ' ' . implode(' ', $this->_svgAttrs) : '')
                . '>';

            // Build each path element
            foreach ($icon as $v) {
                $result .= '<path d="' . $v . '"'
                    . ' class="fa' . (dfCount($this->_class) > 0 ? ' ' . implode(' ', $this->_class) : '') . '"'
                    . (dfCount($this->_attrs) > 0 ? implode(' ', $this->_attrs) : '')
                    . '></path>';
            }

            $result .= '</svg>';

            if (false === $this->_echo) {
                return $result;
            }
            echo $result;
        }

        return $this->_view;
    }

    /**
     * Add a CSS class to the path elements.
     *
     * @param string $class CSS class name
     *
     * @return $this
     */
    public function addClass($class)
    {
        $this->_class[] = $class;
        return $this;
    }

    /**
     * Add a CSS class to the SVG container.
     *
     * @param string $class CSS class name
     *
     * @return $this
     */
    public function addSvgClass($class)
    {
        $this->_svgClass[] = $class;
        return $this;
    }

    /**
     * Add an HTML attribute to the path elements.
     *
     * @param string $attr Complete attribute string (e.g., 'style="color:red"')
     *
     * @return $this
     */
    public function addAttrs($attr)
    {
        $this->_attrs[] = $attr;
        return $this;
    }

    /**
     * Add an HTML attribute to the SVG container.
     *
     * @param string $attr Complete attribute string (e.g., 'width="24"')
     *
     * @return $this
     */
    public function addSvgAttrs($attr)
    {
        $this->_svgAttrs[] = $attr;
        return $this;
    }
}