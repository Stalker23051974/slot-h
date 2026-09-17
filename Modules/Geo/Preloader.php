<?php

namespace Modules\Geo;

use \Modules\Base\Models\DbTables\Person as dbPerson;
use \Application\Helpers\Line as L;
use \Modules\Base\Models\DbTables as db;
use \Application\Assistance\Controller\Controller as Ctr;

/**
 * Preloader for the Geo module.
 *
 * Handles authentication and user initialization for geographic
 * data management. Redirects to login if not authenticated.
 *
 * @package   Modules\Geo
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
     * Checks authentication, loads user data, and validates
     * user status before allowing access.
     *
     * @param \Application\Assistance\View\View $view    View object
     * @param \Application\Assistance\Request   $request Request object
     */
    public function __construct($view, $request)
    {
        // Skip authentication for auth controller
            /** @var \Modules\Base\Models\PersonPrivate $checkAuth */
            if (!($view->currentPerson = dbPerson::getRow($checkAuth->getPersonId()))) {
                \Application\Helpers\Person::quit();
            }

            // Check if user is banned
            if ($view->currentPerson->getPersonStatus() == dbPerson::STATUS_BANNED) {
                L::jump(L::getUrl(\Modules\Free\Bootstrap::$_module, \Modules\Free\Crud::INDEX_CONTROLLER, 'banned'));
            }

            // Load protected user data
            $protected = db\PersonProtected::getByUser($view->currentPerson->_id());
            /** @var \Modules\Base\Models\PersonProtected $protected */

            // Redirect to start page if configured
            $view->currentProtected = $protected;

            // Sync user preferences from cookies to database
            $this->checkPersonUpdates($view->currentPerson, $view->currentProtected);

            // Set user permissions
            $view->currentPerson->setPersonPermissions();

            // Enable translation mode if user has localization permission
            if ($view->currentPerson->checkRules(db\Rule::RULE_LOCALIZATION_CODE) && isset($_COOKIE[Ctr::LOCALIZATION_COOKIE]) && $_COOKIE[Ctr::LOCALIZATION_COOKIE] > 0) {
                \Config\CC::$_adminLocale = [];
            }
    }
}