<?php

namespace Modules\Free\Controllers;

use Config\CC as C;

/**
 * Contact page controller.
 *
 * Displays contact information and social media links.
 * This is a static page that does not require dynamic content.
 *
 * @package   Modules\Free\Controllers
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */
class Contact extends \Application\Assistance\Controller\Controller
{
    /**
     * Constructor.
     *
     * Initializes the controller and defines available actions.
     *
     * @param \Application\Assistance\Request $request Request object
     */
    public function __construct(\Application\Assistance\Request $request)
    {
        parent::__construct($request);
        $this->_actions = [
            parent::INDEX_ACTION => []
        ];
    }

    /**
     * Index action.
     *
     * Renders the Contact page without layout wrapper.
     * Uses project-specific view path and disables the layout
     * for a clean, standalone page.
     */
    public function indexAction()
    {
        $this->setViewPath()
            ->_view->setLayoutFree()
            ->setTitle(C::locale('Контакты SLOT-H'))
            ->setDescription(C::locale('Свяжитесь с автором и сообществом SLOT-H через доступные каналы. В социальных сетях и мессенджерах.'))
            ->setKeywords(C::locale('контакты SLOT-H'))
            ->setOgDescription('Свяжитесь в мессенджерах и социальных сетях.')
        ;
    }
}