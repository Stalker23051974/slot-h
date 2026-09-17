<small>Предыдущие статьи:

- ➡️ [Раздел 11. Модуль Base](/docs/11-module-base)
- ➡️ [Раздел 12. Модуль Free](/docs/12-module-free)
- ➡️ [Раздел 13. Модуль Geo](/docs/13-module-geo)
</small>
# SLOT-H: Создание модуля

Модуль в системе - это самодостаточный блок функциональности, который подключается автоматически при наличии определённой структуры. Для создания нового модуля достаточно добавить папку в директорию `Modules`.

---

## Регистрация модуля

Перед созданием структуры модуля необходимо зарегистрировать его в ядре системы.

В файле `Application/Crud.php` добавить константу с именем модуля:

```php
const MODULE_BLOG = 'Blog';
```
Включить модуль в список активных модулей:

```php
public static $_modules = [
    Crud::MODULE_BASE => true,
    Crud::MODULE_FREE => true,
    Crud::MODULE_GEO => true,
    Crud::MODULE_BLOG => true
];
```
##Базовая структура модуля
Минимальный модуль состоит из трёх обязательных файлов:

Bootstrap.php - определяет имя модуля и доступ для гостей

Crud.php - описывает права доступа и контроллеры

Preloader.php - выполняет предзагрузку (авторизация, проверка прав)

Дополнительные папки добавляются по мере необходимости:

Controllers - контроллеры модуля

Models - модели данных и DbTables

Views - шаблоны для каждого проекта

Public - CSS и JS для каждого проекта

## Пример создания модуля Blog
### 1. Bootstrap.php
```php
<?php

namespace Modules\Blog;

/**
 * Bootstrap class for the Blog module.
 *
 * @package   Modules\Blog
 */
class Bootstrap extends \Application\Bootstrap
{
    /**
     * Module name.
     *
     * @var string
     */
    public static $_module = \Application\Crud::MODULE_BLOG;

    /**
     * Guest access flag.
     *
     * @var bool
     */
    public static $_allow_guest = true;
}
````
### 2. Crud.php
```php
<?php

namespace Modules\Blog;

use Application\Assistance\Controller\Controller as Cr;
use \Config\CC as C;

/**
 * CRUD configuration for the Blog module.
 *
 * @package   Modules\Blog
 */
class Crud extends \Application\Crud
{
    /** Module permission code */
    const MODULE_PERMISSION = 40;

    /** Permission code for Index controller */
    const INDEX_PERMISSION = 101;

    /** Index controller name */
    const INDEX_CONTROLLER = 'Index';

    /**
     * Get localized controller names.
     *
     * @return array Controller permission => localized name
     */
    public static function getNames()
    {
        return [
            self::INDEX_PERMISSION => 'Главная страница блога',
        ];
    }

    /**
     * Controller ID mapping (for permission system).
     *
     * @var array
     */
    public static $_ids = [
        self::INDEX_CONTROLLER => null,
    ];

    /**
     * List of controllers and their permission codes.
     *
     * @var array
     */
    public static $_controllers = [
        self::INDEX_CONTROLLER => self::INDEX_PERMISSION,
    ];

    /**
     * List of modules and their permissions.
     *
     * @var array
     */
    public static $_modules = [
        \Application\Crud::MODULE_BLOG => self::MODULE_PERMISSION
    ];

    /**
     * Access rules for module actions.
     *
     * @var array
     */
    public static $_rules = [
        self::MODULE_PERMISSION => [
            self::INDEX_PERMISSION => [
                Cr::INDEX_ACTION => Cr::READ_RULE
            ],
        ],
    ];
}
````
### 3. Preloader.php
```php
<?php

namespace Modules\Blog;

use Application\Assistance\Controller\TraitGlobal;
use Config\CC as C;
use \Modules\Base\Models\DbTables as db;
use \Application\Assistance\Controller\Controller as Cr;

/**
 * Preloader for the Blog module.
 *
 * @package   Modules\Blog
 */
class Preloader
{
    use \Application\Assistance\Controller\TraitClass;

    /**
     * Initialize the preloader.
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
````
### 4. Добавление контроллера
Контроллеры создаются в папке Controllers. Каждый контроллер наследуется от базового контроллера системы.

```php
<?php

namespace Modules\Blog\Controllers;

use \Modules\Blog\Models\DbTables as db;

/**
 * Blog Index controller.
 *
 * @package   Modules\Blog\Controllers
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
        $this->_actions = [];
    }

    /**
     * Index action.
     */
    public function indexAction()
    {
        $this->setViewPath()
            ->_view->setLayoutFree()
            ->append(['posts' => db\Post::getAll($this->_page)]);
    }
}
````
### 5. Добавление моделей
Модели создаются в папке Models. Для работы с базой данных требуется класс DbTable с описанием таблицы.

#### 5.1 DbTables/Post.php
```php
<?php

namespace Modules\Blog\Models\DbTables;

/**
 * Post database table class.
 *
 * @package   Modules\Blog\Models\DbTables
 *
 * @method static \Modules\Blog\Models\Post|\Modules\Blog\Models\Post[]|array|false getRow($ids = null, $page = false, $order = false, $model = true, $cache = true)
 * @method static \Modules\Blog\Models\Post[]|array|false getAll($page = false, $order = false, $model = true)
 */
class Post extends \Application\Assistance\DatabaseExtend
{
    /** Primary key field */
    const POST_ID = 'post_id';

    /** Post text content */
    const POST_TEXT = 'post_text';

    /** Post author */
    const POST_AUTHOR = 'post_author';

    /** Post creation time */
    const POST_TIME = 'post_time';

    /** Table name */
    public static $_table = 'posts';

    /** Primary key field */
    public static $_index = self::POST_ID;

    /**
     * Field definitions.
     *
     * @var array
     */
    public static $_fields = [
        self::POST_ID => [],
        self::POST_TEXT => [self::FP_TYPE => self::TYPE_TEXT],
        self::POST_AUTHOR => [self::FP_TYPE => self::TYPE_STRING],
        self::POST_TIME => [self::FP_TYPE => self::TYPE_INT],
    ];
}
````

#### 5.2 Models/Post.php
```php
<?php

namespace Modules\Blog\Models;

/**
 * Post model class.
 *
 * @package   Modules\Blog\Models
 *
 * @method int getPostId()
 * @method $this setPostId(int $post_id)
 * @method string getPostText()
 * @method $this setPostText(string $post_text)
 * @method string getPostAuthor()
 * @method $this setPostAuthor(string $post_author)
 * @method int getPostTime()
 * @method $this setPostTime(int $post_time)
 */
class Post extends \Application\Assistance\Model
{
    /** @var int Primary key */
    public $post_id;

    /** @var string Post text */
    public $post_text;

    /** @var string Post author */
    public $post_author;

    /** @var int Post creation time */
    public $post_time;
}
````
#### 5.3 SQL для создания таблицы
```sql
CREATE TABLE IF NOT EXISTS `posts` (
    `post_id` INT(11) NOT NULL AUTO_INCREMENT,
    `post_text` TEXT NOT NULL,
    `post_author` VARCHAR(255) NOT NULL,
    `post_time` INT(11) NOT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 6. Добавление представлений
Представления создаются в папке Views/Project_<номер проекта>/<имя контроллера>. Для каждого проекта могут быть свои представления.

#### 6.1 Пример: Views/Project_1/Index/index.phtml

``` php
<?php
use \Config\CC as C;

/** @var \Modules\Blog\Models\Post[] $posts */
/** @var \Application\Assistance\View\View $this */

$this->addEnvironment();
?>

<!-- код страницы, используя массив постов, переданных через ->append в контроллере -->
```
#### 6.2 Добавление статики
CSS и JS файлы создаются в папках:

Public/css/Project_<номер проекта>/<имя контроллера>/index.css

Public/js/Project_<номер проекта>/<имя контроллера>/index.js

## Структура готового модуля
```text
Modules/Blog/
├── Bootstrap.php
├── Crud.php
├── Preloader.php
├── Controllers/
│   └── Index.php
├── Models/
│   ├── DbTables/
│   │   └── Post.php
│   └── Post.php
├── Public/
│   ├── css/
│   │   └── Project_1/
│   │       └── Index/
│   │           └── index.css
│   └── js/
│       └── Project_1/
│           └── Index/
│               └── index.js
└── Views/
    └── Project_1/
        └── Index/
            └── index.phtml
```
## Завершение
После создания всех классов и файлов необходимо запустить генератор автолоадера:

```bash
php Application/Tools/createAutoloader.php
```
Модуль становится доступным в системе. Можно переходить к настройке внешнего вида представления и стилей - они не влияют на работоспособность модуля.

## Удаление модуля
Для удаления модуля достаточно удалить его папку из Modules и отключить (удалить его в классе Application/Crud). Система автоматически перестанет его загружать.

**Важно**: при удалении модуля необходимо также удалить его таблицы из базы данных, если они были созданы: не нужно плодить мусор в базе!

---

## Что дальше?

- ➡️ [Раздел 15. Структура базы данных](/docs/15-database-diagram)
- ➡️ [Раздел 16. Принципы ORM](/docs/16-orm-principles)
- ➡️ [Раздел 17. Методы работы](/docs/17-orm-methods)
