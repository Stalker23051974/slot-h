<small>Previous articles:

- ➡️ [Section 18. Using JOINs](/docs/18-join)
- ➡️ [Section 19. Models](/docs/19-models)
- ➡️ [Section 20. JSON Data](/docs/20-orm-json)
</small>

# SLOT-H: Controller Types

The system includes several types of controllers, each designed for specific tasks. All controllers inherit from base classes located in the core.

---

## Base Controller

The main controller for working with the web interface. Inherits from `Application\Assistance\Controller\Controller`. Used for pages that render HTML.

The base controller acts as a mentor: it monitors access, checks permissions, manages data, but does not interfere with the action's work. Its job is to prepare the environment, run the action, and after completion, pass the result to the view.

**Available in the base controller:**

- Request object for retrieving parameters
- View object for passing data to templates
- Redirect methods
- User action logging
- Access rights checking

**Base controller lifecycle:**

1. Access rights check
2. Action execution
3. Action returns data
4. Controller passes data to the view
5. Controller finishes work

**Controller example:**

```php
namespace Modules\Blog\Controllers;

class Index extends \Application\Assistance\Controller\Controller
{
    public function __construct($request)
    {
        parent::__construct($request);
    }

    public function indexAction()
    {
        $this->_view->append(['data' => 'Hello World']);
    }
}
```
##API Controller
Designed for creating JSON APIs. Inherits from Application\Assistance\Controller\ApiController.

Features:

Returns data in JSON format

Does not use templating

Works through the ApiView view object

Supports standard response format

Example:

```php
namespace Modules\Blog\Controllers;

class Api extends \Application\Assistance\Controller\ApiController
{

    public function __construct($request)
    {
        parent::__construct($request);
    }

    public function getAction()
    {
        $this->_view->_result = $this->setOk(['data' => 'value']);
    }
}
```
The response will be automatically converted to JSON with checksum and sign fields added.

##Restful Controller
Designed for creating REST APIs. Inherits from Application\Assistance\Controller\RestfulController.

Features:

Supports HTTP methods GET, POST, PUT, DELETE

Works with HTTP response codes

Uses RestfulView for presentation

Example:

```php
namespace Modules\Blog\Controllers;

class Rest extends \Application\Assistance\Controller\RestfulController
{
    public function __construct($request)
    {
        parent::__construct($request);
    }

    public function getAction()
    {
        // GET request handling
    }

    public function postAction()
    {
        // POST request handling
    }
}
```
###CLI Controller
Designed for command-line operations. Inherits from Application\Assistance\Controller\CliController.

Features:

Does not use sessions or cookies

Does not render views

Runs via CLI worker or cron

Used for background tasks

Example:

```php
namespace Modules\Base\Controllers;

class Cli extends \Application\Assistance\Controller\CliController
{
    public function __construct($request)
    {
        parent::__construct($request);
    }

    public function queueAction()
    {
        // queue task processing
    }
}
```
Command-line invocation:

```bash
php Application/Tools/cli.php -stage=project -module=Base -controller=Cli -action=queue
```
##Implementation Features
The controller does not know which model it works with. The programmer explicitly calls the required model in the action code.

All controllers use the TraitClass trait for access to common methods.

Callable actions in controllers must have the Action suffix. For example, indexAction, editAction. Private methods can be named as the developer prefers - as long as it's human-readable.

Access rights are checked using the _access array in the controller. Rights are verified in the module's Preloader before the action is called.

##Creating a New Controller

There are two options:

- Create it manually (just copy-paste an existing one and remove what's not needed);

- Run the console command and follow the script prompts with the required data. As a result, you'll get a controller, views for all actions, as well as CSS and JS files.

```bash
php Application/Tools/createController.php
```


##What's next?
- ➡️ [Раздел 22. Section 22. Frontend Structure](/docs/22-layouts)
- ➡️ [Раздел 23. Section 23. JS Library Choice](/docs/23-js)
- ➡️ [Раздел 24. Section 24. Dynamic Signature](/docs/24-security-sign)
