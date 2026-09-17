<?php

namespace Modules\Free\Controllers;

use Modules\Free\Bootstrap;
use Modules\Free\Crud;
use Modules\Geo\Models\DbTables\Language;
use \Application\Helpers\Line;
use \Config\CC as C;

/**
 * Documentation page controller.
 *
 * Displays documentation or instructions for users.
 * This is a static page that does not require dynamic content.
 *
 * @package   Modules\Free\Controllers
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */
class Docs extends \Application\Assistance\Controller\Controller
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
     * Renders the Documentation page without layout wrapper.
     * Uses project-specific view path and disables the layout
     * for a clean, standalone page.
     */
    public function indexAction()
    {
        $id = (int)($this->_request->getParams('article', '1'));
        if ($id < 9) {
            $id = '0' . $id;
        }
        $language = (Language::getRow(DEFAULT_LANGUAGE))->getLanguageIso1();
        $list = scandir(ROOT_PATH.'docs/'.$language.DIRECTORY_SEPARATOR);
        unset($list[0], $list[1]);
        foreach ($list as $v) {
            if (preg_match("/^".$id."-/", $v)) {
                $file = $v;
                break;
            }
        }
        if (!isset($file)) {
            $file = $list[0];
        }
        $html = Line::markdownToHtml(file_get_contents(ROOT_PATH.'docs/'.$language.DIRECTORY_SEPARATOR.$file));
        $html = preg_replace(["/<a href=\"(\/docs\/)([\d]{1,})([^\"]{1,})/", "/&lt;small&gt;.*?&lt;\/small&gt;/", "/---/"], ['<a href="'.Line::getUrl(Bootstrap::$_module, Crud::DOCS_CONTROLLER, 'index').'/article/$2"', '', '<hr>'], $html);
        $this->_view->setLayoutFree()
            ->append([
                'content' => $html,
                'id' => $id,
                'last' => preg_replace("/[^\d]{1,}/", '', array_pop($list))
            ])
            ->setTitle(C::locale('Документация SLOT-H — архитектура, принципы, руководства'))
            ->setDescription(C::locale('Полная документация SLOT-H: архитектура, принципы работы, ORM, модули, безопасность, локализация, очереди. Всё для быстрого старта и глубокого понимания системы.'))
            ->setKeywords(C::locale('документация SLOT-H, архитектура SLOT-H, принципы SLOT-H, ORM, мультипроектность, PHP экосистема, руководство SLOT-H'))
            ->setOgDescription(C::locale('Документация SLOT-H. Всё, что нужно для установки, настройки и создания собственных модулей.'))
        ;
    }
}