<?php

namespace Modules\Free\Controllers;

use Config\CC as C;
use \Application\Helpers as H;

/**
 * Page controller for static pages.
 *
 * Handles static content pages like Privacy Policy and Terms of Use.
 * Renders pages without layout for standalone display.
 *
 * @package   Modules\Free\Controllers
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */
class Page extends \Application\Assistance\Controller\Controller
{
    use \Application\Assistance\Controller\TraitClass;

    /**
     * Constructor.
     *
     * @param \Application\Assistance\Request $request Request object
     */
    public function __construct(\Application\Assistance\Request $request)
    {
        parent::__construct($request);
        $this->_actions = [];
    }

    /**
     * Privacy Policy page.
     *
     * Displays the privacy policy with SEO metadata and breadcrumbs.
     */
    public function policyAction()
    {
        $this->_view->setLayoutFree()
            ->setListen([
                C::locale('Политика конфиденциальности', false) => H\Line::getUrl(
                    $this->_request->getModule(),
                    $this->_request->getController(),
                    $this->_request->getAction()
                )
            ]);
    }

    /**
     * Terms of Use page.
     *
     * Displays the terms of use with SEO metadata and breadcrumbs.
     */
    public function termsAction()
    {
        $this->_view->setLayoutFree()
            ->setListen([
                C::locale('Правила использования', false) => H\Line::getUrl(
                    $this->_request->getModule(),
                    $this->_request->getController(),
                    $this->_request->getAction()
                )
            ]);
    }

    public function lockedAction()
    {
        $this->setViewPath()->_view->disableHeader()->setLayoutFree()
            ->setTitle(C::locale('Скоро запуск — SLOT-H'))
            ->setDescription(C::locale('SLOT-H готовится к запуску. Следите за обновлениями в социальных сетях и мессенджерах. Запуск запланирован на 14 августа 2026 года.'))
            ->setKeywords(C::locale('SLOT-H запуск, скоро запуск, таймер SLOT-H, 14 августа 2026'))
            ->setOgDescription(C::locale('SLOT-H скоро запустится. Следите за обновлениями — осталось совсем немного времени.'))
        ;
    }
}