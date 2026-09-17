<?php

namespace Modules\Free\Controllers\Index;

use \Config\CC as C;
use \Modules\Base\Models\DbTables as db;
use \Modules\Base\Models\DbTables\AuthCheck as ACheck;
use \Modules\Base\Models\DbTables\PersonPrivate as PP;
use \Application\Helpers as H;

/**
 * Index1 controller for Project 1.
 *
 * Handles the main page and authentication for Project 1.
 * Extends the base Index controller with project-specific logic.
 *
 * @package   Modules\Free\Controllers\Index
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */
class Index1 extends \Modules\Free\Controllers\Index
{
    /**
     * Main index action.
     *
     * Handles login form submission, authentication validation,
     * and renders the main page with login status.
     */
    public function indexAction()
    {
        $result = [];
        $login = $this->_request->getParams('email');
        $pass = $this->_request->getParams('password');
        $append = [
            'login' => $login,
        ];

        // Process login form submission (POST, not search)
        if ($this->_request->isPost() && !isset($_POST['search_text'])) {
            $allows = C::constant(db\Constant::MAX_ATTEMPT_ALLOW);

            if (dfTrim($login) !== '' && dfTrim($pass) !== '') {
                $attempts = false;

                // Check if authentication is disabled
                if ($allows == 0) {
                    $this->_view->setErrors('auth', C::locale('Incorrect login or password'))->_code = self::_VIEW_CODE_AUTH;
                } else if ($attempt = ACheck::checkByHash()) {
                    // Check remaining attempts
                    $attempts = $allows - $attempt->getAuthCheckAttempt();
                    if ($attempts == 0) {
                        $this->authFail(0, \Config\CC::constant(db\Constant::AUTH_TIMEOUT) - CURRENT_TIME + $attempt->getAuthCheckTime());
                    }
                }

                // Validate credentials
                if (false === $attempts || $attempts > 0) {
                    $user = false;

                    if ($protected = PP::getAuthUser($pass, $login)) {
                        /** @var \Modules\Base\Models\PersonPrivate $protected */
                        $user = db\Person::getRow($protected->getPersonId());
                    }

                    if (!is_bool($user)) {
                        if ($user->getPersonStatus() == db\Person::STATUS_ENABLE) {
                            $private = PP::getByUser($user->_id());
                            /** @var \Modules\Base\Models\PersonPrivate $private */
                            /** @var \Modules\Base\Models\PersonPrivate $user */

                            if ($private->getPersonPrivateBanned() == PP::PERSON_ACTIVE) {
                                // Set short session TTL if "remember me" is not checked
                                if (!$this->_request->getParams('login_remember')) {
                                    $_SESSION[\Application\Assistance\Controller\Controller::SESSION_TTL_NAME] = C::constant(db\Constant::SHORT_SESSION_TTL);
                                }

                                H\Person::setAuth($private->getPersonPrivateUid());
                                $_SESSION['login'] = 1;
                                // Demo version: module Main is removed
//                                H\Line::jump(H\Line::getUrl(\Modules\Main\Bootstrap::$_module, \Modules\Main\Crud::INDEX_CONTROLLER));
                            } else {
                                $this->_view->setErrors('auth', C::locale('The user is blocked'))->_code = self::_VIEW_CODE_AUTH;
                            }
                        }
                    }

                    // Record failed attempt
                    if ($allows > 0) {
                        /** @var \Modules\Base\Models\AuthCheck $attempt */
                        if ($attempt == false) {
                            $attempt = (new \Modules\Base\Models\AuthCheck())
                                ->setAuthCheckTime(time())
                                ->setAuthCheckHash(\Application\Helpers\Line::getServerHash())
                                ->setAuthCheckAttempt(1);
                        } else {
                            $attempt->setAuthCheckAttempt($attempt->getAuthCheckAttempt() + 1);
                        }
                        $attempt->save();

                        if ($attempt = ACheck::checkByHash()) {
                            $attempts = $allows - $attempt->getAuthCheckAttempt();
                            $this->authFail($attempts, \Config\CC::constant(db\Constant::AUTH_TIMEOUT));
                        }
                    }
                }
            }

            // Get remaining attempts for display
            $check = $allows > 0 ? ACheck::checkByHash() : false;
            $append['check'] = false === $check || $allows - $check->getAuthCheckAttempt() > 0
                ? true
                : $check->getAuthCheckTime() + \Config\CC::constant(db\Constant::AUTH_TIMEOUT) - CURRENT_TIME;
        }

        $userLogIn = false;

        // Render the view
        $this->setViewPath()->_view->setLayoutFree()->append(['userLogIn' => $userLogIn]);
    }
}