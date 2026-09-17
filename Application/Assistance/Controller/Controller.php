<?php

/**
 * Base controller class for all HTTP request handlers.
 *
 * This is the foundational controller that all application controllers
 * extend. It provides:
 * - Request/response lifecycle management
 * - View initialization and rendering
 * - Access control and permission validation
 * - User logging and activity tracking
 * - Language and localization setup
 * - Parameter validation infrastructure
 * - Common redirect and navigation utilities
 *
 * The controller lifecycle:
 * 1. Constructor validates access permissions
 * 2. Sets up language/voice preferences
 * 3. Initializes the view
 * 4. Executes the requested action method
 * 5. Renders the view with the action's output
 *
 * @package   Application\Assistance\Controller
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Assistance\Controller;

use \Modules\Base\Models\DbTables as bDb;
use \Modules\Geo\Models\DbTables as gDb;
use \Application\Helpers\Line as L;
use \Config\CC as C;

class Controller
{
    use \Application\Assistance\Controller\TraitClass;

    // ============================================================================
    // PERMISSION RULE CONSTANTS
    // ============================================================================

    /**
     * Rule calculation: controller value + 10x, where x = user rule.
     * Controller code length: 3 characters.
     * Action code length: 3 characters.
     *
     * Example: controller = 100, user rule:
     * 100      - access denied
     * 100001   - read only
     * 100002   - full access
     */

    /** Read-only permission level */
    const READ_RULE = 1;

    /** Full CRUD permission (create, read, delete) */
    const CREATE_RULE = 2;

    // ============================================================================
    // PROPERTIES
    // ============================================================================

    /** @var \Application\Assistance\View\View View object for rendering */
    public $_view;

    /** @var \Application\Assistance\Request Request object with all input data */
    public $_request;

    /** @var bool Whether to use project-specific view paths */
    protected $_useProject = false;

    /** @var string Page title */
    public $_title = '';

    /** @var array Action-specific configurations */
    public $_actions = [];

    /** @var array Access control rules for actions */
    public $_access = [];

    // ============================================================================
    // STANDARD ACTION CONSTANTS
    // ============================================================================

    /** Default index/list action */
    const INDEX_ACTION = 'index';

    /** Edit/create action */
    const EDIT_ACTION = 'edit';

    /** Delete/remove action */
    const DELETE_ACTION = 'remove';

    /** Pagination page parameter name */
    const PAGE_PARAM = 'page';

    /** Insert parameter name */
    const INSET_PARAM = 'inset';

    /** Reload parameter name */
    const RELOAD_PARAM = 'reload';

    // ============================================================================
    // SESSION AND COOKIE CONSTANTS
    // ============================================================================

    /** Hash/signature parameter name for CSRF protection */
    const HASH_NAME = 'hash';

    /** API hash parameter name */
    const API_HASH_NAME = 'current_hash';

    /** Session TTL parameter name */
    const SESSION_TTL_NAME = 'session_ttl';

    /** God mode (user impersonation) hash parameter */
    const GOD_MODE = 'parent_hash';

    /** Translation mode hash parameter */
    const TRANSLATE_MODE = 'translate_hash';

    /** Session key for preventing duplicate page entries */
    const PREVENT_PAGE = 'prevent';

    /** Cookie name for short menu preference */
    const SHORT_MENU_COOKIE = 'short_menu';

    // ============================================================================
    // LOGGING CONSTANTS
    // ============================================================================

    /** @var \Modules\Base\Models\PersonLog User activity log model */
    public $_log;

    /** @var array Track changes for logging */
    public $_change = [];

    /** @var \Application\Helpers\Person\InterfacePerson Person helper instance */
    public $_personHelper;

    /** @var \Application\Assistance\Database $_database */
    public $_database;

    /** @var \Application\Assistance\Model $_model */
    public $_model;

    /** File attachment type: image */
    const FILE_APPEND_IMAGE = false;

    /** File attachment type: document */
    const FILE_APPEND_DOCUMENTS = 1;

    /** File attachment type: audio */
    const FILE_APPEND_AUDIO = 2;

    // ============================================================================
    // USER LOG TYPES
    // ============================================================================

    /** Guest/non-authenticated user action */
    const USERLOG_GUEST = 1;

    /** Authentication change (login/logout) */
    const USERLOG_AUTH_CHANGE = 2;

    /** List view page display */
    const USERLOG_SHOW_LIST = 3;

    /** Object removal/deletion */
    const USERLOG_REMOVE_OBJECT = 4;

    /** Entity creation form opened */
    const USERLOG_ADD_ENTITY = 5;

    /** Entity creation or editing */
    const USERLOG_EDIT_ENTITY = 6;

    /** Entity view details */
    const USERLOG_VIEW_ENTITY = 7;

    /** Suffix appended to action method names */
    const ACTION_SUFFIX = 'Action';

    // ============================================================================
    // COOKIE NAMES
    // ============================================================================

    /** Language preference cookie (project-specific) */
    const FREE_LANGUAGE_COOKIE = '__language__' . CURRENT_PROJECT;

    /** GMT offset cookie */
    const FREE_GMT_COOKIE = '__gmt__' . CURRENT_PROJECT;

    /** Voice preference cookie */
    const FREE_VOICE_COOKIE = '__voice__' . CURRENT_PROJECT;

    /** Voice type cookie */
    const FREE_VOICE_TYPE_COOKIE = '__vtype__' . CURRENT_PROJECT;

    /** Voice enable/disable cookie */
    const FREE_VOICE_ENABLE_COOKIE = '__venable__' . CURRENT_PROJECT;

    /** Country preference cookie */
    const COUNTRY_COOKIE = '__country__' . CURRENT_PROJECT;

    /** Cookie consent/agreement cookie */
    const AGREE_COOKIE = '__cookie__' . CURRENT_PROJECT;

    /** Navigation preference cookie */
    const NAVIGATION_COOKIE = '__class__' . CURRENT_PROJECT;

    /** User identifier cookie */
    const USER_COOKIE = '__user__' . CURRENT_PROJECT;

    /** Localization mode cookie */
    const LOCALIZATION_COOKIE = '__localization__' . CURRENT_PROJECT;

    /** Currency preference cookie */
    const CURRENCY_COOKIE = '__currency__' . CURRENT_PROJECT;

    // ============================================================================
    // SESSION KEYS
    // ============================================================================

    /** Session key for import content progress */
    const SESSION_IMPORT_ONLINE = 'importContent';

    /** Session key for import TTL tracking */
    const SESSION_IMPORT_TTL = 'importTtl';

    // ============================================================================
    // VIEW CODES
    // ============================================================================

    /** Authentication view code */
    const _VIEW_CODE_AUTH = 1;

    /** Remind password view code */
    const _VIEW_CODE_REMIND = 2;

    /** Member registration view code */
    const _VIEW_CODE_REGISTER_MEMBER = 3;

    /** Password restoration view code */
    const _VIEW_CODE_RESTORE_PASSWORD = 7;

    // ============================================================================
    // UTILITY METHODS
    // ============================================================================

    /**
     * Get localized user log type descriptions.
     *
     * @param bool|int $code Specific log type code, or false for all
     *
     * @return array|string Localized description
     */
    public function constUserLog($code = false)
    {
        $res = [
            self::USERLOG_GUEST          => 'Guest',
            self::USERLOG_AUTH_CHANGE    => 'Login',
            self::USERLOG_SHOW_LIST      => 'List view: %s page',
            self::USERLOG_REMOVE_OBJECT  => 'Delete object',
            self::USERLOG_ADD_ENTITY     => 'Opening a creation form',
            self::USERLOG_EDIT_ENTITY    => 'Creation / Editing',
            self::USERLOG_VIEW_ENTITY    => 'View'
        ];
        return false === $code ? $res : $res[$code];
    }

    // ============================================================================
    // DEFAULT ACTIONS
    // ============================================================================

    /**
     * Default index action.
     * Override in child controllers to provide list/grid functionality.
     */
    public function indexAction()
    {
    }

    /** @var int Current page number for pagination */
    public $_page = 0;

    // ============================================================================
    // CONSTRUCTOR - MAIN CONTROLLER LIFECYCLE
    // ============================================================================

    /**
     * Initialize the controller and process the request.
     *
     * The constructor orchestrates the entire request lifecycle:
     *
     * 1. Set up person helper and request references
     * 2. Resolve model and view class names
     * 3. Initialize the view
     * 4. Run module preloader (permission checks)
     * 5. Set up language preferences
     * 6. Activate user logging
     * 7. Validate access permissions for the action
     * 8. Execute the requested action method
     * 9. Save activity log
     * 10. Render the view
     *
     * @param \Application\Assistance\Request $request Parsed request object
     */
    public function __construct($request)
    {
        // ====================================================================
        // INITIALIZATION
        // ====================================================================

        $this->_personHelper = PERSON_HELPER;
        $this->_request = $request;

        /**
         * Resolve the database table class and model class
         * based on the current controller's namespace.
         */
        $class = get_called_class();
        $this->_database = preg_replace(
            '/Controllers/',
            \Application\Crud::MODEL_FOLDER . '\\' . \Application\Crud::DATABASE_FOLDER,
            $class
        );
        $this->_model = preg_replace(
            '/Controllers/',
            \Application\Crud::MODEL_FOLDER,
            $class
        );

        // ====================================================================
        // VIEW INITIALIZATION
        // ====================================================================

        /**
         * Create the view object.
         * Base modules use a shared view path, others use module-specific.
         */
        $this->_view = (new \Application\Assistance\View\View(
            dfInArray($request->getModule(), \Application\Crud::getBaseModules())
        ));

        /**
         * Check fingerprint for security validation.
         */
        if (isset($_REQUEST['_fp_'])) {
            \Application\Helpers\Line::checkFingerprint($this->_view);
        }

        $this->_view->_request = $request;

        /**
         * Load module CRUD configuration.
         */
        $crud = '\\' . \Application\Crud::MODULE_FOLDER . '\\' . $request->getModule() . '\Crud';
        /** @var \Modules\Base\Crud $crud */

        /**
         * Extract current page from request parameters.
         */
        $this->_page = $request->getParams(self::PAGE_PARAM, 0);

        // ====================================================================
        // MODULE PRELOADER
        // ====================================================================

        /**
         * Run module-specific preloader.
         * Handles global permission checks and module initialization.
         */
        $preloader = \Application\Crud::MODULE_FOLDER . '\\' . $request->getModule() . '\\Preloader';
        new $preloader($this->_view, $request);

        // ====================================================================
        // LANGUAGE SETUP
        // ====================================================================

        /**
         * Get active languages from the database.
         */
        $activeL = gDb\Language::getActive();

        /**
         * Handle language switching via 'lng' parameter.
         * Updates cookie and session state.
         */
        if ($lng = $this->_request->getParams('lng')) {
            $lng = strtolower($lng);
            foreach ($activeL as $v) {
                if ($v->getLanguageIso1() == $lng) {
                    setcookie(
                        self::FREE_LANGUAGE_COOKIE,
                        $v->_id(),
                        CURRENT_TIME + C::constant(bDb\Constant::SESSION_LIFETIME),
                        '/'
                    );
                    setcookie(self::FREE_LANGUAGE_COOKIE, $v->_id(), C::constant(bDb\Constant::SESSION_LIFETIME) + CURRENT_TIME, '/');
                    \Application\Translate::$_languageId = $v->_id();
                    \Application\Translate::$_cache = null;
                    \Config\CC::$_lng = $lng;
                    define('DEFAULT_LANGUAGE', $v->_id());
                    break;
                }
            }
        }

        /**
         * Set default language from cookie or configuration.
         */
        if (!defined('DEFAULT_LANGUAGE')) {
            if (!isset($_COOKIE[self::FREE_LANGUAGE_COOKIE])
                || !isset($activeL[$_COOKIE[self::FREE_LANGUAGE_COOKIE]])) {
                setcookie(
                    self::FREE_LANGUAGE_COOKIE,
                    C::get('core_default_language'),
                    CURRENT_TIME + C::constant(bDb\Constant::SESSION_LIFETIME),
                    '/'
                );
                $_COOKIE[self::FREE_LANGUAGE_COOKIE] = C::get('core_default_language');
                define('DEFAULT_LANGUAGE', C::get('core_default_language'));
            } else {
                define('DEFAULT_LANGUAGE', $_COOKIE[self::FREE_LANGUAGE_COOKIE]);
            }
        }

        // ====================================================================
        // VOICE SETUP
        // ====================================================================

        /**
         * Determine voice preference.
         * Priority: user profile > cookie > default configuration.
         */
        define(
            'DEFAULT_VOICE',
            ($this->_view->currentPerson && (int)($this->_view->currentPerson->getVoiceId() > 0))
                ? $this->_view->currentPerson->getVoiceId()
                : (isset($_COOKIE[self::FREE_VOICE_COOKIE])
                ? $_COOKIE[self::FREE_VOICE_COOKIE]
                : C::get('core_default_gender'))
        );

        /**
         * Define text direction (LTR/RTL) for the current language.
         */
        define(
            'TEXT_DIRECTION',
            (\Modules\Geo\Models\DbTables\Language::getRow(DEFAULT_LANGUAGE))
                ->getLanguageDirection()
        );

        // ====================================================================
        // USER LOGGING SETUP
        // ====================================================================

        $baseModule = dfInArray($this->_request->getModule(), \Application\Crud::getBaseModules());
        $this->activateUserLog(
            $request,
            $request->getModule(),
            $request->getController(),
            $request->getAction(),
            true === $baseModule
        );

        // ====================================================================
        // ACCESS CONTROL VALIDATION
        // ====================================================================

        /**
         * Validate access permissions for the requested action.
         * Uses rules defined in $this->_access.
         */
        if (isset($this->_access[$this->_request->getAction()])
            && true !== $this->_access[$this->_request->getAction()]) {

            $check = true === $this->_access[$this->_request->getAction()][0];

            foreach ($this->_access[$this->_request->getAction()][1] as $v) {
                if (!$this->_view->currentPerson) {
                    $check = false;
                    break;
                }
                if (false === $this->_access[$this->_request->getAction()][0]) {
                    // OR logic: any rule grants access
                    if (true === $this->_view->currentPerson->checkRules($v)) {
                        $check = true;
                        break;
                    }
                } else {
                    // AND logic: all rules must pass
                    if (false === $this->_view->currentPerson->checkRules($v)) {
                        $check = false;
                        break;
                    }
                }
            }
        }

        // ====================================================================
        // ACTION DISPATCH
        // ====================================================================

        /**
         * Validate that the action method exists.
         * Fall back to index action if not found.
         */
        if (!method_exists($this, $request->getAction() . self::ACTION_SUFFIX)) {
            $request->setAction(Controller::INDEX_ACTION);
        }

        /**
         * Execute the requested action method.
         */
        $this->{$request->getAction() . self::ACTION_SUFFIX}();

        // ====================================================================
        // LOGGING AND VIEW RENDERING
        // ====================================================================

        /**
         * Save change log for the user action.
         */
        ($this->_personHelper)::setLogChange($baseModule, $this);
        $this->_log->save();

        /**
         * Prepare view data and render.
         */
        $projectPath = DIRECTORY_SEPARATOR . 'Project_' . USE_PROJECT;

        $this->_view->append([
            '__index'    => Controller::INDEX_ACTION,
            '__edit'     => Controller::EDIT_ACTION,
            '__delete'   => Controller::DELETE_ACTION,
            '__page'     => Controller::PAGE_PARAM,
            '__inset'    => Controller::INSET_PARAM,
            '__reload'   => Controller::RELOAD_PARAM,
            '_page'      => $this->_page,
            '_projectPath' => $projectPath,
            '_path' => implode(
                    DIRECTORY_SEPARATOR,
                    dfExplode(
                        '\\',
                        preg_replace(
                            '/Controllers/',
                            'Views' . ($this->_useProject === false ? $projectPath : DIRECTORY_SEPARATOR . 'Project_' . CURRENT_PROJECT),
                            preg_replace("/[\d]{1,}/", '', $class)
                        )
                    )
                ) . DIRECTORY_SEPARATOR . $request->getAction() . '.' . $this->_view->_viewExtension,
            'controller' => $this->_request->getController(),
            '_allow_guest' => $this->_request->_allow_guest
        ], false, false, false);

        /**
         * Store last URL for navigation history.
         */
        if (true === C::$_setLastUrl) {
            setcookie(
                \Application\Helpers\Person::LAST_URL_COOKIE . CURRENT_PROJECT,
                $this->_request->requestUrl,
                CURRENT_TIME + C::constant(bDb\Constant::SESSION_LIFETIME),
                '/'
            );
        }

        /**
         * Render the final output.
         */
        $this->_view->show();
    }

    /**
     * Toggle project-specific view path.
     *
     * @param bool $v True to use project-specific views, false for shared
     *
     * @return $this
     */
    protected function setViewPath($v = true)
    {
        $this->_useProject = $v;
        return $this;
    }

    // ============================================================================
    // USER LOGGING
    // ============================================================================

    /**
     * Initialize user activity logging.
     *
     * @param \Application\Assistance\Request $request   Request object
     * @param string                          $module    Current module
     * @param string                          $controller Current controller
     * @param string                          $action    Current action
     * @param bool                            $base      Whether this is a base module
     */
    private function activateUserLog($request, $module, $controller, $action, $base)
    {
        $crud = '\\' . \Application\Crud::MODULE_FOLDER . '\\' . $module . '\Crud';
        /** @var \Modules\Base\Crud $crud */

        $controllers = \Application\Crud::getControllers();
        $controller = $controllers[$module][$controller];

        $set = ($this->_personHelper)::activateUserLog(
            $base,
            $request,
            $this->_view,
            $crud,
            $controller,
            $module,
            $action
        );

        /**
         * Pass module/controller metadata to the view.
         */
        $this->_view->append([
            'module'               => $crud::MODULE_PERMISSION,
            'controllerPermission' => $controller,
            'controller'           => $controller,
            'action'               => $action
        ], false, false, false);

        /**
         * Log the list view page display.
         */
        if ($action == Controller::INDEX_ACTION) {
            $this->_change[] = sprintf(
                $this->constUserLog(Controller::USERLOG_SHOW_LIST),
                (int)($this->_page) + 1
            );
        }

        ($this->_personHelper)::setLogModel($base, $set, $this);
    }

    // ============================================================================
    // REDIRECT METHODS
    // ============================================================================

    /**
     * Redirect to a URL or back to the previous page.
     *
     * If no parameters are provided, redirects to the previous page.
     * Otherwise, builds a URL using the provided module/controller/action.
     *
     * @param string|null $module    Target module (null = current module)
     * @param string|null $controller Target controller (null = current controller)
     * @param string|null $action    Target action (null = default action)
     * @param array       $params    Additional URL parameters
     */
    public function redirect($module = null, $controller = null, $action = null, $params = [])
    {
        if ($module === null) {
            /**
             * Redirect to the previous page (from navigation history).
             */
            L::jump($this->_view->getBackUrl());
        } else {
            /**
             * Build and redirect to a specific URL.
             */
            L::jump(L::getUrl(
                (is_null($module) ? $this->_request->getModule() : $module),
                (is_null($controller) ? $this->_request->getController() : $controller),
                is_null($action) ? C::get('default_action') : $action,
                $params
            ));
        }
    }

    /**
     * Redirect to the default page for the project.
     */
    public function defaultPage()
    {
        L::jump(L::getUrl(
            C::get('default_module'),
            C::get('default_controller'),
            C::get('default_action')
        ));
    }
}