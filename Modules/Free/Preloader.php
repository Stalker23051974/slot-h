<?php

namespace Modules\Free;

use \Modules\Base\Models\DbTables as db;
use \Application\Assistance\Controller\Controller as Cr;

/**
 * Preloader for the Free module.
 *
 * Handles authentication and user initialization for public pages.
 * Sets up the current user session and permissions.
 *
 * @package   Modules\Free
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */
class Preloader
{
    use \Application\Assistance\Controller\TraitClass;

    /**
     * Initialize the preloader.
     *
     * Checks authentication, loads user data, and sets up
     * permissions for the current session.
     *
     * @param \Application\Assistance\View\View $view    View object
     * @param \Application\Assistance\Request   $request Request object
     */
    public function __construct($view, $request)
    {
        // Check authentication via session/cookie
        if ($view->currentPrivate = \Modules\Base\Models\DbTables\PersonPrivate::checkAuth()) {
            // Load user data
            $view->currentPerson = $view->currentPrivate->linkPersonId();
            $view->currentProtected = $view->currentPerson->lotsPersonProtectedInBaseByPersonId();

            // Sync user preferences from cookies to database
            $this->checkPersonUpdates($view->currentPerson, $view->currentProtected);

            // Set user permissions
            $view->currentPerson->setPersonPermissions();

            // Enable translation mode if user has localization permission
            if ($view->currentPerson->checkRules(db\Rule::RULE_LOCALIZATION_CODE) && isset($_COOKIE[Cr::LOCALIZATION_COOKIE]) && $_COOKIE[Cr::LOCALIZATION_COOKIE] > 0) {
                \Config\CC::$_adminLocale = [];
            }
        }
    }
}