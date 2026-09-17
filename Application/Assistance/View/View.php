<?php

/**
 * Main view renderer for HTML output.
 *
 * This class handles the presentation layer of the application,
 * providing methods for:
 * - Rendering HTML templates (.phtml files)
 * - Managing CSS and JavaScript assets
 * - Layout composition (header, footer, content)
 * - Asset caching and versioning via static file system
 * - Language and localization support
 * - SEO metadata (title, description, keywords, Open Graph)
 * - Breadcrumb navigation
 * - Form label and icon generation
 *
 * The view is the bridge between controllers and the final HTML output.
 * It collects data from the controller and renders it using templates.
 *
 * @package   Application\Assistance\View
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Assistance\View;

use \Application\Assistance\Controller\Controller as C;
use Application\Helpers\Line;
use Application\Helpers\Line as hL;
use \Modules\Base\Models\DbTables as dB;
use \Modules\Free\Bootstrap as fB;
use \Modules\Free\Crud as fC;
use \Config\CC as CC;

class View
{
    // ============================================================================
    // LOCALIZATION TYPES
    // ============================================================================

    /** Localization type: short string (e.g., labels, button text) */
    const LOCALIZATION_STRING = 1;

    /** Localization type: long text (e.g., articles, descriptions) */
    const LOCALIZATION_TEXT = 2;

    // ============================================================================
    // PROPERTIES
    // ============================================================================

    /** @var string View file extension (.phtml) */
    public $_viewExtension = 'phtml';

    /** @var string Path to the current view file */
    public $_path;

    /** @var array Data container passed to view templates */
    public $_container = [];

    /** @var array Global navigation menus */
    public $_globalMenus = [];

    /** @var array Available languages for the project */
    public $_languages = [];

    /** @var bool|array Cached static file references */
    public $_layout = true;

    /** @var string Page title */
    public $_title = 'mpf';

    /** @var \Modules\Base\Models\Person Current authenticated user */
    public $currentPerson = false;

    /** @var \Modules\Base\Models\PersonProtected Current user's protected data */
    public $currentProtected = false;

    /** @var \Modules\Base\Models\PersonPrivate Current user's private data */
    public $currentPrivate = false;

    /** @var mixed Session authorization data */
    protected $_s_a;

    /** @var int Entity ID for the current view */
    public $entityId;

    /** @var string Current module name */
    public $module;

    /** @var string Current controller name */
    public $controller;

    /** @var string Current controller permission code */
    public $controllerPermission;

    /** @var string Current action name */
    public $action;

    /** @var bool Whether this is an admin view */
    protected $_admin = false;

    /** @var string Project-specific path for views */
    public $_projectPath = '';

    /** @var array Validation errors */
    public $_errors = [];

    /** @var int HTTP status code */
    public $_code = 0;

    /** @var bool Whether to include Google Maps */
    public $_useMap = false;

    /** @var \Application\Assistance\Request Request object */
    public $_request = false;

    /** @var array Static file cache from database */
    private $_staticFiles = [];

    /** @var array JavaScript collection for output */
    public $_jsCollection = [];

    /** @var bool Whether to show the menu */
    public $_menu_ = true;

    public $_allow_guest = false;
    public $__index;
    public $__edit;
    public $__delete;
    public $__page;
    public $_page;
    public $__inset;
    public $__reload;

    public $_meta;

    /**
     * Initialize the view.
     *
     * @param bool $admin Whether this is an admin view
     */
    public function __construct($admin = false)
    {
        $this->_admin = $admin;
        $this->_staticFiles = db\StaticFile::getAll();
        $this->_meta = new Meta($this);
    }

    /**
     * Set a validation error for a field.
     *
     * @param string $field Field name
     * @param string $error Error message
     *
     * @return $this
     */
    public function setErrors($field, $error)
    {
        $this->_errors[$field] = $error;
        return $this;
    }

    // ============================================================================
    // SEO AND STRUCTURED DATA
    // ============================================================================

    /** @var array SEO data including breadcrumb list */
    public $_seo = ['listen' => [[]], 'description' => '', 'keywords' => ''];

    /**
     * Set breadcrumb navigation for structured data.
     *
     * @param array $data Key-value pairs for breadcrumb items
     *
     * @return $this
     */
    public function setListen($data)
    {
        foreach ($data as $k => $v) {
            $this->_seo['listen'][] = [
                '@type' => 'ListItem',
                'position' => dfCount($this->_seo['listen']) + 1,
                'name' => $k,
                'item' => $v
            ];
        }
        return $this;
    }

    /**
     * Set meta description for SEO.
     *
     * @param string $desc Meta description
     *
     * @return $this
     */
    public function setDescription($desc)
    {
        $this->_meta->setDescription($desc);
        return $this;
    }

    /**
     * Set meta keywords for SEO.
     *
     * @param string $list Comma-separated keywords
     *
     * @return $this
     */
    public function setKeywords($list)
    {
        $this->_meta->setKeywords($list);
        return $this;
    }

    // ============================================================================
    // OPEN GRAPH AND CANONICAL
    // ============================================================================

    /** @var string Open Graph URL */
    public $ogUrl = '';

    /** @var string Open Graph description */
    public $ogDescription = '';

    /** @var string Open Graph image URL */
    public $ogImage = '';

    /** @var string Canonical URL */
    public $canonicalHref = '';

    /** @var string Meta keywords */
    public $keywords = '';

    /**
     * Set Open Graph URL.
     *
     * @param string $url OG URL
     *
     * @return $this
     */
    public function setOgUrl($url)
    {
        $this->ogUrl = $url;
        return $this;
    }

    /**
     * Set Open Graph description.
     *
     * @param string $desc OG description
     *
     * @return $this
     */
    public function setOgDescription($desc)
    {
        $this->_meta->setOgDescription($desc);
        return $this;
    }

    /**
     * Set Open Graph image.
     *
     * @param string $image OG image URL
     *
     * @return $this
     */
    public function setOgImage($image)
    {
        $this->ogImage = $image;
        return $this;
    }

    /**
     * Set canonical URL.
     *
     * @param string $link Canonical link
     *
     * @return $this
     */
    public function setCanonicalHref($link)
    {
        $this->canonicalHref = $link;
        return $this;
    }

    /**
     * Disable the header/menu for this view.
     *
     * @return $this
     */
    public function disableHeader()
    {
        $this->_menu_ = false;
        return $this;
    }

    // ============================================================================
    // ASSET MANAGEMENT (CSS/JS)
    // ============================================================================

    /**
     * Add JavaScript files to the view.
     *
     * Supports both application-wide and module-specific scripts.
     * Files are served through the static file handler to prevent caching.
     *
     * @param string|array $names Script name(s) with optional module flag
     * @param bool         $ab   A/B testing variant
     *
     * @return $this
     */
    public function addScript($names = null, $ab = false)
    {
        if (null === $names) {
            $names = $this->action;
        }
        if (!is_array($names)) {
            $names = [$names => true];
        }

        foreach ($names as $name => $module) {
            if (is_string($module)) {
                $path = DIRECTORY_SEPARATOR . 'Project_' . CURRENT_PROJECT;
                $module = $module === 'true';
                $chProject = CURRENT_PROJECT;
            } else {
                $path = $this->_projectPath;
                $chProject = USE_PROJECT;
            }

            /**
             * Build file path based on type.
             * - false: application-level (Application/Public/js)
             * - true: module-level (within module Public/js)
             */
            $file = (false === $module
                ? 'Application/Public/js' . $path . '/' . $name
                : preg_replace([
                    "/([^\/]{1,})$/",
                    "/Views/",
                    "/(Project_[\d]{1,})/"
                ], [
                    $name,
                    'Public/js/',
                    'Project_' . $chProject
                ], $this->_path));

            /**
             * Apply A/B testing variant if enabled.
             */
            if ($ab === true && file_exists($file . $this->_request->_ab . (true === OBFUSCATE_JS && true === $module ? '.min.js' : '.js'))) {
                $file .= $this->_request->_ab;
            }

            /**
             * Add minification for module scripts if enabled.
             */
            $file .= (true === OBFUSCATE_JS && true === $module ? '.min.js' : '.js');

            /**
             * Generate cache-busting hash if not exists.
             */
            if (!isset($this->_staticFiles['/' . $file])) {
                $this->getRand('/' . $file);
            }

            /**
             * Add script tag to collection.
             */
            $this->_jsCollection[] = '<script src="'
                . hL::getUrl(fB::$_module, fC::SCOPE_CONTROLLER, 'get', ['static' => $this->_staticFiles['/' . $file] . '.js'], false)
                . '"></script>';
        }
        return $this;
    }

    /**
     * Add CSS files to the view.
     *
     * @param string|array $names CSS file name(s) with optional module flag
     * @param bool         $ab    A/B testing variant
     *
     * @return $this
     */
    public function addStyle($names = null, $ab = false)
    {
        if (null === $names) {
            $names = $this->action;
        }
        if (!is_array($names)) {
            $names = [$names => true];
        }

        foreach ($names as $name => $module) {
            if (is_string($module)) {
                $path = DIRECTORY_SEPARATOR . 'Project_' . CURRENT_PROJECT;
                $module = $module === 'true';
                $chProject = CURRENT_PROJECT;
            } else {
                $path = $this->_projectPath;
                $chProject = USE_PROJECT;
            }

            /**
             * Build file path for CSS.
             */
            $file = (false === $module
                ? 'Application/Public/css' . $path . '/' . $name
                : preg_replace([
                    "/([^\/]{1,})$/",
                    "/Views/",
                    "/(Project_[\d]{1,})/"
                ], [
                    $name,
                    'Public/css/',
                    'Project_' . $chProject
                ], $this->_path));

            $_file = $file;

            /**
             * Apply A/B testing variant.
             */
            if (true === $ab && file_exists($file . $this->_request->_ab . '.css')) {
                $file .= $this->_request->_ab;
            }

            $file .= '.css';

            /**
             * Generate cache-busting hash if not exists.
             */
            if (!isset($this->_staticFiles['/' . $file])) {
                $this->getRand('/' . $file);
            }

            /**
             * Output link tag directly.
             */
            echo '<link rel="stylesheet" type="text/css" href="',
            hL::getUrl(fB::$_module, fC::SCOPE_CONTROLLER, 'get', ['static' => $this->_staticFiles['/' . $file] . '.css'], false),
            '">';
        }
        return $this;
    }

    /**
     * Add both CSS and JS for the current environment.
     *
     * Convenience method for adding both styles and scripts
     * with the same name/action.
     *
     * @param string $name File name base
     *
     * @return $this
     */
    public function addEnvironment($name = null)
    {
        if (null === $name) {
            $name = $this->action;
        }
        return $this->addStyle([$name => true])->addScript([$name => true]);
    }

    // ============================================================================
    // STATIC FILE CACHE MANAGEMENT
    // ============================================================================

    /**
     * Generate or retrieve a cache-busting hash for a static file.
     *
     * Stores the hash in the database for consistent versioning.
     *
     * @param string|null $path File path
     *
     * @return string Cache-busting query string or empty
     */
    public function getRand($path = null)
    {
        if ($path !== null) {
            if (!isset($this->_staticFiles[$path])) {
                $this->_staticFiles[$path] = md5($path . CURRENT_TIME);
                (new \Modules\Base\Models\StaticFile())
                    ->setStaticFileHash($this->_staticFiles[$path])
                    ->setStaticFilePath($path)
                    ->save();
            }
            return '?rand=' . $this->_staticFiles[$path];
        }
        return '';
    }

    // ============================================================================
    // TITLE AND NAVIGATION
    // ============================================================================

    /**
     * Set the page title and update breadcrumb navigation.
     *
     * @param string $title Page title
     * @param int    $level Breadcrumb truncation level (0 = clear all)
     *
     * @return $this
     */
    public function setTitle($title, $level = 0)
    {
        $this->_meta->setTitle($title);
        $_SESSION['crumbs' . CURRENT_PROJECT] = 0 === $level
            ? []
            : dfArraySlice($_SESSION['crumbs' . CURRENT_PROJECT], 0, $level);
        $_SESSION['crumbs' . CURRENT_PROJECT][] = [
            $title,
            $_SERVER['REQUEST_URI']
        ];
        return $this;
    }

    /**
     * Generate a URL for the given module/controller/action.
     *
     * @param string|null $module     Module name
     * @param string|null $controller Controller name
     * @param string|null $action     Action name
     * @param array       $params     Additional parameters
     *
     * @return string Generated URL
     */
    public function url($module = null, $controller = null, $action = null, $params = [])
    {
        return hL::getUrl(
            is_null($module) ? CC::get('default_module') : $module,
            is_null($controller) ? CC::get('default_controller') : $controller,
            is_null($action) ? CC::get('default_action') : $action,
            $params
        );
    }

    // ============================================================================
    // THIRD-PARTY LIBRARY HELPERS
    // ============================================================================

    /**
     * Add jQuery Autocomplete library.
     *
     * @return $this
     */
    public function addAutocomplete()
    {
        return $this->addStyle(['../autocomplete' => false])
            ->addScript(['../jquery.autocomplete.min' => false]);
    }

    /**
     * Add Chosen select enhancement library.
     *
     * @return $this
     */
    public function addChosen()
    {
        return $this->addStyle(['../chosen' => false])
            ->addScript(['../chosen.jquery.min' => false]);
    }

    /**
     * Add Bootstrap Toggle library.
     *
     * @return $this
     */
    public function addToggle()
    {
        return $this->addStyle(['bootstrap/bootstrap-toggle.min' => false])
            ->addScript(['bootstrap/bootstrap-toggle.min' => false]);
    }

    /**
     * Add Spectrum color picker library.
     *
     * @return $this
     */
    public function addColor()
    {
        return $this->addStyle(['spectrum' => false])
            ->addScript(['../spectrum' => false]);
    }

    /**
     * Add Datetime picker library with localization.
     *
     * Injects localized month and day names for the datepicker.
     *
     * @return $this
     */
    public function addDatetime()
    {
        $monthReal = \Application\Helpers\Date::monthTitleReal();
        unset($monthReal[0]);

        echo '<script>datetimeText = {months: ' . json_encode(array_values($monthReal))
            . ', dayOfWeek: ' . json_encode([
                CC::locale('Su'),
                CC::locale('Mo'),
                CC::locale('Tu'),
                CC::locale('We'),
                CC::locale('Th'),
                CC::locale('Fr'),
                CC::locale('Sa')
            ]) . '}</script>';

        return $this->addStyle(['../datetime' => false])
            ->addScript(['../datetime.min' => false]);
    }

    /**
     * Add CKEditor 5 WYSIWYG editor.
     *
     * @return $this
     */
    public function addCkEditor()
    {
        return $this->addScript(['../ckeditor5/build/ckeditor' => false]);
    }

    /**
     * Add jQuery MultiSelect library.
     *
     * @return $this
     */
    public function addMultiselect()
    {
        $this->addStyle(['../multi-select' => false]);
        $this->addScript(['../jquery.multi-select' => false]);
        return $this;
    }

    // ============================================================================
    // NAVIGATION AND BACK URL
    // ============================================================================

    /**
     * Get the back URL for navigation.
     *
     * Returns the URL of the previous page from the breadcrumb history.
     *
     * @param string|null $module     Module name
     * @param string|null $controller Controller name
     * @param string|null $action     Action name
     * @param array       $params     Additional parameters
     *
     * @return string Back URL
     */
    public function getBackUrl($module = null, $controller = null, $action = null, $params = [])
    {
        if ($module === null && isset($_SESSION[C::PREVENT_PAGE . CURRENT_PROJECT])) {
            $module = $_SESSION[C::PREVENT_PAGE . CURRENT_PROJECT][0];
            $controller = $_SESSION[C::PREVENT_PAGE . CURRENT_PROJECT][1];
        }

        $url = hL::getUrl($module, $controller, $action, $params);

        /**
         * Preserve page parameter for pagination.
         */
        if (isset($_SESSION[C::PREVENT_PAGE . CURRENT_PROJECT])
            && $module === $_SESSION[C::PREVENT_PAGE . CURRENT_PROJECT][0]
            && $controller === $_SESSION[C::PREVENT_PAGE . CURRENT_PROJECT][1]
            && $_SESSION[C::PREVENT_PAGE . CURRENT_PROJECT][2] > 0) {
            $url .= (dfCount($params) > 0 ? '&' : '?') . C::PAGE_PARAM . '=' . $_SESSION[C::PREVENT_PAGE . CURRENT_PROJECT][2];
        }

        return $url;
    }

    /**
     * Enable Google Maps integration.
     *
     * @return $this
     */
    public function useMap()
    {
        $this->_useMap = true;
        return $this;
    }

    // ============================================================================
    // DATA CONTAINER MANAGEMENT
    // ============================================================================

    /**
     * Append data to the view container.
     *
     * @param bool|mixed $data     Data array or value
     * @param bool|mixed $key      Specific key if data is scalar
     * @param bool|mixed $value    Value for specific key
     * @param bool       $container Whether to store in _container (true) or as property (false)
     *
     * @return $this
     */
    public function append($data = false, $key = false, $value = false, $container = true)
    {
        if (true === $container) {
            if (false !== $data) {
                foreach ($data as $key => $value) {
                    $this->_container[$key] = $value;
                }
            } else {
                $this->_container[$key] = $value;
            }
        } else {
            if (false !== $data) {
                foreach ($data as $key => $value) {
                    $this->{$key} = $value;
                }
            } else {
                $this->{$key} = $value;
            }
        }
        return $this;
    }

    /**
     * Disable layout (render only content, no header/footer).
     *
     * @return $this
     */
    public function setLayoutFree()
    {
        $this->_layout = false;
        return $this;
    }

    // ============================================================================
    // LAYOUT RENDERING
    // ============================================================================

    /**
     * Include a layout block template.
     *
     * @param string     $block   Block name (head, foot, granted, etc.)
     * @param array|null $params  Parameters to pass to the template
     * @param bool       $ab      A/B testing variant
     * @param string     $project Project ID
     *
     * @return $this
     */
    public function appendLayoutBlock($block, $params = null, $ab = false, $project = USE_PROJECT)
    {
        if (null !== $params) {
            foreach ([
                         'result', 'name', 'title', 'type', 'customLanguages',
                         'languages', 'appendName', 'canEdit', 'custom'
                     ] as $k) {
                if (isset($params[$k])) {
                    ${'' . $k} = $params[$k];
                }
            }
        }

        include(ROOT_PATH . 'Application' . DIRECTORY_SEPARATOR . 'Layout' . DIRECTORY_SEPARATOR . 'Project_' . $project
            . DIRECTORY_SEPARATOR . 'Skin_' . (
            false !== $this->currentProtected
                ? $this->currentProtected->getData(db\PersonProtected::SKIN, CC::get('base_skin'))
                : CC::get('base_skin')
            )
            . DIRECTORY_SEPARATOR . $block . ($ab === true ? $this->_request->_ab : '')
            . '.' . $this->_viewExtension);

        return $this;
    }

    // ============================================================================
    // FORM HELPERS
    // ============================================================================

    /**
     * Add a form label with optional hint icon.
     *
     * @param string      $id    Field ID
     * @param string      $text  Label text
     * @param string|null $class CSS class
     * @param string|null $attrs Additional HTML attributes
     * @param bool        $hint  Whether to show hint icon
     *
     * @return $this
     */
    public function addLabel($id, $text, $class = null, $attrs = null, $hint = true)
    {
        $hash = md5($this->module . '_' . $this->controllerPermission . '_' . $this->action . '_' . $id);

        echo '<label'
            . (null !== $class ? ' class="' . $class . '"' : '')
            . (null !== $attrs ? ' ' . $attrs : '')
            . ' for="' . $id . '">'
            . $text
            . ((true === $hint) ? $this->addIcon(Icon::HINT, null, false)->addSvgAttrs('__hf' . '="' . $hash . '"')->add() : '')
            . '</label>';

        return $this;
    }

    /**
     * Get an SVG icon path for the current project.
     *
     * @param string $svg SVG file name
     *
     * @return string Full URL to the SVG
     */
    public function addSvg($svg)
    {
        return ROOT . '/Application/Public/svgs/Project_' . USE_PROJECT . '/' . $svg;
    }

    /**
     * Create a new Icon instance for rendering.
     *
     * @param int         $type  Icon type constant
     * @param string|null $id    Optional ID
     * @param bool        $echo  Whether to echo directly
     *
     * @return Icon Icon renderer
     */
    public function addIcon($type, $id = null, $echo = true)
    {
        return new Icon($this, $type, $id, $echo);
    }

    /**
     * Format an array as a string representation for debugging.
     *
     * @param array      $arr  Array to format
     * @param bool|null $type Output format type
     *
     * @return string String representation
     */
    public function addStringValues($arr, $type = null)
    {
        if (!is_array($arr)) {
            $arr = [$arr];
        }

        $s = [];
        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                $s[] = ($type === null ? $k . '=' : ($type === true ? '' : $k . ':'))
                    . $this->addStringValues($v, dfArrayKeys($v)[0] === (int)(dfArrayKeys($v)[0]));
            } else {
                $s[] = ($type === null ? $k . '=' : ($type === true ? '' : $k . ':'))
                    . ($v === (int)($v) ? $v : "'" . dfAddslashes(Line::devilsBit($v)) . "'");
            }
        }

        return ($type === null ? '' : ($type === true ? '[' : '{'))
            . implode(',', $s)
            . (($type === null ? '' : ($type === true ? ']' : '}')));
    }

    // ============================================================================
    // MAIN RENDER METHOD
    // ============================================================================

    /**
     * Render the complete HTML page.
     *
     * This method orchestrates the full rendering process:
     * 1. Sets up SEO structured data with breadcrumb
     * 2. Extracts container data into variables
     * 3. Renders the header layout block
     * 4. Renders the main content template
     * 5. Renders Google Maps if enabled
     * 6. Renders JavaScript collection
     * 7. Renders the footer layout block
     *
     * All $_REQUEST and $_POST data is cleared after processing
     * for security and to prevent reuse.
     */
    public function show()
    {
        /**
         * Initialize breadcrumb with home page.
         */
        $this->_seo['listen'][0] = [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => CC::locale('Главная', false),
                'item' => $this->url(fB::$_module, fC::INDEX_CONTROLLER, 'index')
            ]
        ];

        /**
         * Clear request data to prevent re-use.
         */
        $_REQUEST = [];
        $_POST = [];

        /**
         * Extract container data into local variables.
         */
        if (dfCount($this->_container) > 0) {
            foreach ($this->_container as $k => $v) {
                ${'' . $k} = $v;
            }
        }

        /**
         * Render the header.
         */
        $ext = false === $this->_layout ? 'Ext' : '';
        $this->appendLayoutBlock('head' . $ext, null, false, CURRENT_PROJECT);

        /**
         * Render the main layout if enabled.
         */
        if (true === $this->_layout) {
            $this->appendLayoutBlock('granted');
        }

        /**
         * Normalize the view path.
         */
        $this->_path = preg_replace([
            "/" . $this->_request->getController() . "\/" . $this->_request->getController() . "/",
            "/Modules\//"
        ], [
            $this->_request->getController(),
            'Modules/'
        ], $this->_path);

        /**
         * Redirect if the view file doesn't exist.
         */
        if (!file_exists($this->_path)) {
            \Application\Helpers\Line::jump($this->url());
        }

        /**
         * Render the main content template.
         */
        require_once($this->_path);

        /**
         * Render Google Maps if enabled.
         */
        if (true === $this->_useMap) {
            echo '<script src="//maps.googleapis.com/maps/api/js?key='
                . CC::get('google_map_apikey')
                . '&language=' . \Modules\Geo\Models\DbTables\Language::getRow(DEFAULT_LANGUAGE)->getLanguageIso1()
                . '&callback=initMap" async defer></script>';
        }

        /**
         * Render JavaScript collection.
         */
        echo implode('', $this->_jsCollection);

        /**
         * Render the footer.
         */
        $this->appendLayoutBlock('foot' . $ext, null, false, CURRENT_PROJECT);
    }
}