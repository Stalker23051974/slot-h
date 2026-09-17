<small>Previous articles:

- ➡️ [Section 11. Base Module](/docs/11-module-base)
- ➡️ [Section 12. Free Module](/docs/12-module-free)
- ➡️ [Section 13. Geo Module](/docs/13-module-geo)
</small>

# SLOT-H: Creating a Module

A module in the system is a self-contained functional block that loads automatically when a specific structure is present. To create a new module, simply add a folder to the `Modules` directory.

---

## Module Registration

Before creating the module structure, it must be registered in the system kernel.

In the `Application/Crud.php` file, add a constant with the module name:

```php
const MODULE_BLOG = 'Blog';
```
Enable the module in the active modules list:

```php
public static $_modules = [
    Crud::MODULE_BASE => true,
    Crud::MODULE_FREE => true,
    Crud::MODULE_GEO => true,
    Crud::MODULE_BLOG => true
];
```
##Basic Module Structure
A minimal module consists of three required files:

Bootstrap.php - defines the module name and guest access

Crud.php - describes access rights and controllers

Preloader.php - performs preloading (authentication, permission checks)

Additional folders are added as needed:

Controllers - module controllers

Models - data models and DbTables

Views - templates for each project

Public - CSS and JS for each project

##Example: Creating a Blog Module
###1. Bootstrap.php
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
###2. Crud.php
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
            self::INDEX_PERMISSION => 'Blog main page',
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
###3. Preloader.php
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
###4. Adding a Controller
Controllers are created in the Controllers folder. Each controller inherits from the system's base controller.

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
###5. Adding Models
Models are created in the Models folder. A DbTable class describing the table is required for database operations.

####5.1 DbTables/Post.php
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
####5.2 Models/Post.php
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
####5.3 SQL for creating the table
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
````
###6. Adding Views
Views are created in the Views/Project_<project_number>/<controller_name> folder. Each project can have its own views.

####6.1 Example: Views/Project_1/Index/index.phtml
```php
<?php
use \Config\CC as C;

/** @var \Modules\Blog\Models\Post[] $posts */
/** @var \Application\Assistance\View\View $this */

$this->addEnvironment();
?>

<!-- Page code using the posts array passed via ->append in the controller -->
```
####6.2 Adding Static Files
CSS and JS files are created in the folders:

Public/css/Project_<project_number>/<controller_name>/index.css

Public/js/Project_<project_number>/<controller_name>/index.js

Complete Module Structure
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
##Completion
After creating all classes and files, run the autoloader generator:

```bash
php Application/Tools/createAutoloader.php
```
The module becomes available in the system. You can now proceed to customize the view appearance and styles - they do not affect the module's functionality.

##Module Removal
To remove a module, simply delete its folder from Modules and disable it (remove it from the Application/Crud.php class). The system will automatically stop loading it.

Important: when removing a module, you must also delete its tables from the database if they were created - don't leave database clutter!

---

##What's next?

- ➡️ [Section 15. Database Structure](/docs/15-database-diagram)
- ➡️ [Section 16. ORM Principles](/docs/16-orm-principles)
- ➡️ [Section 17. Working Methods](/docs/17-orm-methods)
