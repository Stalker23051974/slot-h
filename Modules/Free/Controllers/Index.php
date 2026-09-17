<?php

namespace Modules\Free\Controllers;

use \Config\CC as C;
use \Application\Helpers as H;

/**
 * Base Index controller for public pages.
 *
 * Handles public-facing actions including authentication,
 * error pages, sitemap generation, and user logout.
 *
 * @package   Modules\Free\Controllers
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */
class Index extends \Application\Assistance\Controller\Controller
{
    /**
     * Constructor.
     *
     * @param \Application\Assistance\Request $request Request object
     */
    public function __construct(\Application\Assistance\Request $request)
    {
        parent::__construct($request);
        $this->_actions = [
            parent::EDIT_ACTION => []
        ];
    }

    /**
     * Handle authentication failure with remaining attempts.
     *
     * @param int $attempt Remaining attempts
     * @param int $time    Time until next attempt (seconds)
     */
    protected function authFail($attempt, $time = 0)
    {
        if ($attempt === 0) {
            $this->_view->setErrors('auth', sprintf(C::locale('The limit of authorization attempts has been exceeded. You can continue after %s seconds. I suggest you reset your password.'), $time))->_code = self::_VIEW_CODE_REMIND;
        } else {
            $this->_view->setErrors('auth', sprintf(C::locale('Invalid credentials. You have %s left'), $attempt))->_code = self::_VIEW_CODE_AUTH;
        }
    }

    /** Action for password change via UID */
    const ACTION_PASSWORD_CHANGE = 'uid';

    /**
     * Banned user page.
     *
     * Displays a message for banned users without layout.
     */
    public function bannedAction()
    {
        $this->_view->_layout = false;
    }

    /**
     * Locked/maintenance page.
     *
     * Displays the maintenance page when ENGINEERING_WORKS is enabled.
     */
    public function lockedAction()
    {
        die(preg_replace([
            "/~/",
            "/230574/"
        ], [
            ROOT . 'Images/favicon_' . CURRENT_PROJECT,
            C::get('yandex_metrica')
        ], file_get_contents(ROOT_PATH . 'Application/Layout/locked.html')));
    }

    /** @var array Sitemap XML structure */
    private $_sitemap = [
        [
            0,
            '<?xml version="1.0" encoding="UTF-8"?>'
        ],
        [
            1,
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
        ]
    ];

    /**
     * Add a URL entry to the sitemap.
     *
     * @param string $page Page URL
     * @param string $date Last modification date
     */
    private function createXmlUrl($page, $date)
    {
        $this->_sitemap[] = [
            2,
            '<url>'
        ];
        $this->_sitemap[] = [
            3,
            '<loc>' . $page . '</loc>'
        ];
        $this->_sitemap[] = [
            3,
            '<lastmod>' . $date . '</lastmod>'
        ];
        $this->_sitemap[] = [
            3,
            '<changefreq>monthly</changefreq>'
        ];
        $this->_sitemap[] = [
            3,
            '<priority>0.9</priority>'
        ];
        $this->_sitemap[] = [
            2,
            '</url>'
        ];
    }

    /**
     * Logout action.
     *
     * Clears user session and redirects to the main page.
     */
    public function quitAction()
    {
        H\Person::quit();
        $this->redirect(\Modules\Free\Bootstrap::$_module, \Modules\Free\Crud::INDEX_CONTROLLER, 'index');
    }
}