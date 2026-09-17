<?php

namespace Modules\Free\Controllers;

use Config\CC as C;

/**
 * About page controller.
 *
 * Displays information about the project or company.
 * This is a static page that does not require dynamic content.
 *
 * @package   Modules\Free\Controllers
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */
class About extends \Application\Assistance\Controller\Controller
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
     * Renders the About page without layout wrapper.
     * Uses project-specific view path and disables the layout
     * for a clean, standalone page.
     */
    public function indexAction()
    {
        $this->setViewPath()
            ->_view->setLayoutFree()
            ->setTitle(C::locale('О SLOT-H — история, философия, автор'))
            ->setDescription(C::locale('SLOT-H — экосистема для прагматичной разработки мультипроектных систем. Создана на основе 25-летнего опыта, 15+ лет в продакшене. История, философия и принципы.'))
            ->setKeywords(C::locale('о SLOT-H, история SLOT-H, автор SLOT-H, философия SLOT-H, принципы SLOT-H, 25 лет опыта'))
            ->setOgDescription('История и философия SLOT-H. Почему система появилась, как устроена и для кого создана.')
        ;
    }
}