<?php

// use translate

namespace Modules\Free\Controllers;

use \Config\CC as C;
use \Modules\Base\Models\DbTables as db;
use \Application\Helpers as H;
use Modules\Geo\Models\Language;

/**
 * Person API controller for public user actions.
 *
 * Handles user-related API endpoints including:
 * - Online presence tracking
 * - Notifications and alerts
 * - Form submission protection
 * - Chat messages
 * - Password reminder
 *
 * @package   Modules\Free\Controllers
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */
class Person extends \Application\Assistance\Controller\ApiController
{
    use \Application\Assistance\Controller\TraitClass;

    /**
     * Getter action - retrieves user state and notifications.
     *
     * Returns:
     * - User alerts/notifications
     * - Online users list
     * - Form submission cooldown status
     * - Chat messages
     * - Chat authentication status
     * - Message counters
     */
    public function getterAction()
    {
        $res = false;

        // Get or create user active session
        if ($this->_view->currentPerson) {
            $res = db\PersonActive::getByPerson($this->_view->currentPerson->_id());
        } else if (isset($_COOKIE['PHPSESSID'])) {
            $res = db\PersonActive::getBySession($_COOKIE['PHPSESSID']);
        }

        $person = $this->_view->currentPerson ? $this->_view->currentPerson->_id() : null;

        if ($res === false) {
            $res = (new \Modules\Base\Models\PersonActive())
                ->setPersonId($person)
                ->setPersonActiveSession($_COOKIE['PHPSESSID']);
        } else {
            if ($res->getPersonId() != $person) {
                $res->setPersonId($person);
            }
        }
        $res->setPersonActiveDate(CURRENT_TIME)->save();

        // Get user notifications
        $notifications = [];

        /**
         * Demo version: Push notifications disabled
         */
//        if ($res = db\PersonAlert::getByPerson($person)) {
//            foreach ($res as $v) {
//                $value = dfJsonDecode($v->getPersonAlertData(), true);
//                $notifications[$v->getPersonAlertType()] = is_array($value)
//                    ? preg_replace(dfArrayKeys($value), dfArrayValues($value), \Application\Helpers\Push::getByTag($v->getPersonAlertType()))
//                    : \Application\Helpers\Push::getByTag($v->getPersonAlertType());
//            }
//        }

        // Form submission cooldown
        $checkForm = 0;
        $chat = [];

        /**
         * Demo version: Device fingerprint disabled
         */
//        if ($this->_view->_fp_ != false) {
//            if ($check = CF::checkByHash($this->_view->_fp_)) {
//                if (dfCount($check) >= C::constant(db\Constant::CHECK_FORM_NUMBER)) {
//                    $checkForm = C::constant(db\Constant::CHECK_FORM_TIMEOUT) * 60 - (CURRENT_TIME - array_pop($check)->getCheckFormTime());
//                }
//            }
//
//            // Get chat messages for this fingerprint
//            if ($messages = M::getByFingerprint($this->_view->_fp_)) {
//                if (dfCount($messages) > 0) {
//                    array_map(function($v) use (&$chat) {
//                        /** @var \Modules\PsPress\Models\Message $v */
//                        $chat[] = [
//                            'manager' => $v->getMessageAuthor() > 0,
//                            'text' => $v->getMessageText(),
//                            'time' => \Application\Helpers\Date::getShowerTime($v->getMessageTime()),
//                            'read' => $v->getMessageReadUser() > 0,
//                            'uid' => md5($v->_id())
//                        ];
//                    }, $messages);
//                }
//            }
//        }

        // Build and return response
        $this->_view->_result = $this->setOk([
            'alerts' => $notifications,
            'online' => db\PersonActive::getOnline(),
            'checkForm' => $checkForm,
            'chat' => $chat,
            'chatAuth' => false, //!!MU::getByFingerprint($this->_view->_fp_), // Demo-version: disabled
            'counters' => [
                'messages' => 0
            ]
        ]);
    }

    /**
     * Password reminder action.
     *
     * Sends a password reminder email to the user if the email exists.
     *
     * @return void
     */
    public function remindAction()
    {
        if ($email = $this->_request->getParams('email')) {

            /**
             * Demo-version: Mail engines disabled
             */
//            if ($private = db\PersonPrivate::getByLogin($email)) {
//                $engine = H\Mail\Mail::getClass([db\MailTemplate::MAIL_TEMPLATE_TYPE => db\MailTemplate::TYPE_SMTP]);
//                $engine = new $engine();
//                /** @var \Application\Helpers\Mail\Smtp $engine */
//                $engine->send(
//                    db\MailTemplate::MAIL_REMIND_PASSWORD,
//                    [
//                        H\Mail\MailPattern::PATTERN_USER_LOGIN => $email,
//                        H\Mail\MailPattern::PATTERN_USER_PASSWORD => H\Line::decrypt(
//                            $private->linkPersonId()->lotsPersonProtectedInBaseByPersonId()->getData(db\PersonProtected::PERSON_PASSWORD),
//                            $private->getPersonPrivateUid()
//                        )
//                    ],
//                    ['email' => $email],
//                    $private->getPersonId()
//                );
//            }
        }
    }

    public function docAction() {
        $search = trim($this->_request->getParams('search'));
        $result = [];
        if (strlen($search) > 2) {
            $language = (\Modules\Geo\Models\DbTables\Language::getRow(DEFAULT_LANGUAGE))->getLanguageIso1();
            $search = escapeshellarg($search);
            $output = shell_exec('grep -l '.$search.' '.ROOT_PATH . 'docs/'.$language.'/*.md 2>/dev/null');
            if ($output) {
                $result = array_map(function($v) {
                    return preg_replace("/[^\d]{1,}/",'', $v);
                }, array_filter(explode("\n", trim($output))));
            }
        }
        $this->_view->_result = $this->setOk(['result' => $result]);
    }
}