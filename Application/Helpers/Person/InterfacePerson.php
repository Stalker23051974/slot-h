<?php

/**
 * Person helper interface for user logging and activity tracking.
 *
 * This interface defines the contract for project-specific person helpers
 * that handle user activity logging, change tracking, and audit trails.
 *
 * Each project can have its own implementation of this interface
 * (e.g., Project1, Project51) to customize logging behavior while
 * maintaining a consistent API across the system.
 *
 * The methods cover:
 * - User activity logging (page views, actions)
 * - Change tracking (before/after state comparison)
 * - API request logging
 * - Log model initialization and persistence
 *
 * @package   Application\Helpers\Person
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Helpers\Person;

interface InterfacePerson
{
    /**
     * Initialize user activity logging for a request.
     *
     * This method is called during controller initialization to set up
     * the logging context for the current user action.
     *
     * @param bool                                         $base       Whether the module is a base module
     * @param \Application\Assistance\Request             $request    Current request object
     * @param \Application\Assistance\View\View           $view       View object for context
     * @param \Modules\Base\Crud                          $crud       Module CRUD configuration
     * @param string                                      $controller Controller permission code
     * @param string                                      $module     Module name
     * @param string                                      $action     Action name
     *
     * @return array Log setup data
     */
    public static function activateUserLog($base, $request, $view, $crud, $controller, $module, $action);

    /**
     * Attach the log model to the controller.
     *
     * Sets the PersonLog model instance on the controller for later
     * persistence during the request lifecycle.
     *
     * @param bool                                         $base       Whether the module is a base module
     * @param array                                        $set        Log data from activateUserLog
     * @param \Application\Assistance\Controller\Controller $controller Controller instance
     */
    public static function setLogModel($base, $set, $controller);

    /**
     * Record change tracking data for the current action.
     *
     * Captures the before/after state of entities being modified
     * and prepares the change description for the log.
     *
     * @param bool                                         $base       Whether the module is a base module
     * @param \Application\Assistance\Controller\Controller $controller Controller instance containing $_before and $_change
     */
    public static function setLogChange($base, $controller);

    /**
     * Save a log entry for API requests.
     *
     * Records API calls with their module, controller, action, entity,
     * and a description of the operation performed.
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
    public static function saveApiLog($view, $moduleCode, $module, $controllerCode, $controller, $action, $entity, $description);

    /**
     * Save a user activity log entry.
     *
     * Records general user actions with a human-readable description.
     * Used for both web and API contexts.
     *
     * @param \Application\Assistance\View\View|\Application\Assistance\View\ApiView $view        View object
     * @param \Application\Assistance\Request                                       $request     Current request
     * @param int                                                                   $module      Module code
     * @param int                                                                   $controller  Controller code
     * @param string                                                                $description Change/action description
     */
    public static function saveLog($view, $request, $module, $controller, $description);
}