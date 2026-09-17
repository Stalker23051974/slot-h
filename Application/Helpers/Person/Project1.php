<?php

/**
 * Project 64 person helper implementation.
 *
 * This class provides project-specific user logging and activity tracking
 * for Project 64. It extends the base Person helper and implements the
 * InterfacePerson contract.
 *
 * Project helpers are project-specific implementations that can override
 * default behavior for user logging, activity tracking, and audit trails.
 * Each project can have its own helper to customize logging logic while
 * maintaining the same interface.
 *
 * @package   Application\Helpers\Person
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Helpers\Person;

use \Modules\Base\Models\DbTables\PersonLog;
use \Application\Assistance\Controller\Controller;

class Project1 extends \Application\Helpers\Person implements InterfacePerson
{
    /**
     * Initialize user activity logging for a request.
     *
     * Prepares the log data structure for the current user action.
     * Captures user ID, IP address, module/controller/action context,
     * and permission codes.
     *
     * @param bool                                         $base       Whether the module is a base module
     * @param \Application\Assistance\Request             $request    Current request object
     * @param \Application\Assistance\View\View           $view       View object for context
     * @param \Modules\Base\Crud                          $crud       Module CRUD configuration
     * @param string                                      $controller Controller permission code
     * @param string                                      $module     Module name
     * @param string                                      $action     Action name
     *
     * @return array Log data array for PersonLog model
     */
    public static function activateUserLog($base, $request, $view, $crud, $controller, $module, $action)
    {
        return [
            PersonLog::PERSON_ID => false !== $view->currentPerson ? $view->currentPerson->_id() : '-1',
            PersonLog::PERSON_LOG_NAME => false !== $view->currentPerson ? $view->currentPerson->_name() : Controller::USERLOG_GUEST,
            PersonLog::PERSON_LOG_IP => ip2long($request->_ip),
            PersonLog::PERSON_LOG_TIME => CURRENT_TIME,
            PersonLog::PERSON_LOG_MODULE_CODE => $crud::MODULE_PERMISSION,
            PersonLog::PERSON_LOG_CONTROLLER_CODE => $controller > 0 ? $controller : '-1',
            PersonLog::PERSON_LOG_MODULE => $module,
            PersonLog::PERSON_LOG_CONTROLLER => $request->getController(),
            PersonLog::PERSON_LOG_ACTION => $action,
            PersonLog::PERSON_LOG_ARCHIVE => null
        ];
    }

    /**
     * Attach the log model to the controller.
     *
     * Creates a new PersonLog model instance with the provided log data
     * and assigns it to the controller's _log property for later saving.
     *
     * @param bool                                         $base       Whether the module is a base module
     * @param array                                        $set        Log data from activateUserLog
     * @param \Application\Assistance\Controller\Controller $controller Controller instance
     */
    public static function setLogModel($base, $set, $controller)
    {
        $controller->_log = new \Modules\Base\Models\PersonLog($set);
    }

    /**
     * Record change tracking data for the current action.
     *
     * Sets the change description on the log model, including any
     * God mode indication if the user is impersonating another user.
     *
     * @param bool                                         $base       Whether the module is a base module
     * @param \Application\Assistance\Controller\Controller $controller Controller instance containing _change array
     */
    public static function setLogChange($base, $controller)
    {
        $controller->_log->setPersonLogChange(
            (isset($_COOKIE[Controller::GOD_MODE . CURRENT_PROJECT]) ? 'In god mode' . '. ' : '')
            . implode("\n", $controller->_change)
        );
    }

    /**
     * Save a log entry for API requests.
     *
     * Records API calls with their full context, including:
     * - User identification (ID and name)
     * - IP address
     * - Module/controller/action names and codes
     * - Entity being operated on
     * - Description of the operation
     * - God mode indication if active
     *
     * @param \Application\Assistance\View\View|\Application\Assistance\View\ApiView $view          View object
     * @param int                                                                     $moduleCode    Module permission code
     * @param string                                                                  $module        Module name
     * @param int                                                                     $controllerCode Controller permission code
     * @param string                                                                  $controller    Controller name
     * @param string                                                                  $action        Action name
     * @param string                                                                  $entity        Entity identifier
     * @param string                                                                  $description   Log description
     */
    public static function saveApiLog($view, $moduleCode, $module, $controllerCode, $controller, $action, $entity, $description)
    {
        (new \Modules\Base\Models\PersonLog([
            PersonLog::PERSON_ID => false !== $view->currentPerson ? $view->currentPerson->_id() : '-1',
            PersonLog::PERSON_LOG_NAME => false !== $view->currentPerson ? $view->currentPerson->_name() : Controller::USERLOG_GUEST,
            PersonLog::PERSON_LOG_IP => ip2long($_SERVER['REMOTE_ADDR']),
            PersonLog::PERSON_LOG_TIME => CURRENT_TIME,
            PersonLog::PERSON_LOG_MODULE_CODE => $moduleCode,
            PersonLog::PERSON_LOG_CONTROLLER_CODE => $controllerCode,
            PersonLog::PERSON_LOG_MODULE => substr($module, 0, 512),
            PersonLog::PERSON_LOG_CONTROLLER => substr($controller, 0, 512),
            PersonLog::PERSON_LOG_ACTION => substr($action, 0, 512),
            PersonLog::PERSON_LOG_ENTITY => $entity,
            PersonLog::PERSON_LOG_CHANGE => (isset($_COOKIE[Controller::GOD_MODE . CURRENT_PROJECT]) ? 'God mode' . '. ' : '') . $description,
            PersonLog::PERSON_LOG_ARCHIVE => null
        ]))->save();
    }

    /**
     * Save a user activity log entry.
     *
     * Records general user actions with a human-readable description.
     * Used for both web and API contexts. Handles string truncation to
     * prevent exceeding database field length limits.
     *
     * @param \Application\Assistance\View\View|\Application\Assistance\View\ApiView $view        View object
     * @param \Application\Assistance\Request                                       $request     Current request
     * @param int                                                                   $module      Module code
     * @param int                                                                   $controller  Controller code
     * @param string                                                                $description Change/action description
     */
    public static function saveLog($view, $request, $module, $controller, $description)
    {
        (new \Modules\Base\Models\PersonLog([
            PersonLog::PERSON_ID => false !== $view->currentPerson ? $view->currentPerson->_id() : '-1',
            PersonLog::PERSON_LOG_NAME => false !== $view->currentPerson ? $view->currentPerson->_name() : Controller::USERLOG_GUEST,
            PersonLog::PERSON_LOG_IP => ip2long($_SERVER['REMOTE_ADDR']),
            PersonLog::PERSON_LOG_TIME => CURRENT_TIME,
            PersonLog::PERSON_LOG_MODULE_CODE => $module,
            PersonLog::PERSON_LOG_CONTROLLER_CODE => $controller > 0 ? $controller : '-1',
            PersonLog::PERSON_LOG_MODULE => substr($request->getModule(), 0, 512),
            PersonLog::PERSON_LOG_CONTROLLER => substr($request->getController(), 0, 512),
            PersonLog::PERSON_LOG_ACTION => substr($request->getAction(), 0, 512),
            PersonLog::PERSON_LOG_CHANGE => (isset($_COOKIE[Controller::GOD_MODE . CURRENT_PROJECT]) ? 'God mode' . '. ' : '') . $description,
            PersonLog::PERSON_LOG_ARCHIVE => $request->getAction()
        ]))->save();
    }
}