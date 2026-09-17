<small>Previous articles:

- ➡️ [Section 19. Models](/docs/19-models)
- ➡️ [Section 20. JSON Data](/docs/20-orm-json)
- ➡️ [Section 21. Controller Types](/docs/21-controllers)
</small>

# SLOT-H: Frontend Structure

The frontend part of the system is built on phtml templates, CSS, and JavaScript. All files are organized by module and tied to specific projects.

---

## Load Order

Frontend loading occurs in a strictly defined sequence:

### 1. Header

First, the file `Application/Layout/Project_<project_number>/Skin_<skin_number>/headerExt.phtml` is loaded for unauthenticated users, or `header.phtml` for authenticated users in the full version.

This file forms the frontend startup data: it includes base CSS and JS, defines global variables, and sets meta tags.

After this, `Application/Layout/Project_<project_number>/Skin_<skin_number>/html.phtml` is called, which contains the general page structure starting with the `<html>` tag. In the demo version, this may seem odd, but the logic is justified: `headerExt` is loaded for unauthenticated users, while `header.phtml` is used for authenticated users, adding its own blocks after the actual HTML begins.

### 2. Main View

The controller and action view is loaded.

Example: `/Modules/Free/Views/Project_<project_number>/Index/index.phtml`

This is the main page content rendered within the HTML structure defined in the layout.

### 3. Footer

The footer is loaded last: `Application/Layout/Project_<project_number>/Skin_<skin_number>/footerExt.phtml`

For authenticated users in the full version, `footer.phtml` is used.

---

## Folder Structure

Each module contains the following folders:

- `Views` - phtml templates
- `Public/css` - styles
- `Public/js` - scripts

Inside each of these folders, a subfolder `Project_<project_number>` is created, and within it, a controller folder.

### Views
```text
The Views folder contains phtml files that render HTML pages.
Views/
└── Project_<project_number>/
    └── <controller_name>/
        ├── index.phtml
        ├── edit.phtml
        └── ...
```

Each project has its own folder. This allows customizing the appearance for a specific project without changing controller logic.

### Public/css

The Public/css folder contains styles. The structure is similar to Views:
```text
Public/css/
└── Project_<project_number>/
    └── <controller_name>/
        ├── index.css
        ├── edit.css
        └── ...
```

### Public/js

The Public/js folder contains JavaScript files:
```text
Public/js/
└── Project_<project_number>/
    └── <controller_name>/
        ├── index.js
        ├── edit.js
        └── ...
```

---

## Including Static Files

In phtml files, static files are included via view methods:

- `$this->addStyle()` - includes CSS
- `$this->addScript()` - includes JS
- `$this->addEnvironment()` - includes both CSS and JS

If no file name is passed, files with the current action name are included.

Example:

```php
$this->addEnvironment();
```
This will include index.css and index.js for the current controller and project.

You can also vary CSS/JS inclusion. Example from /Modules/Blog/Views/Project_1/Index/index.phtml:

```php
$this->addStyle(['first' => true, 'second' => true, 'global' => false])
```
This call will include the following scripts:

- /Modules/Blog/Public/css/Project_1/Index/first.css - true searches in the controller folder
- /Modules/Blog/Public/css/Project_1/Index/second.css - same
- /Application/Public/css/Project_1/global.css - false redirects search to the project's main styles folder

Static files are served through the Scope controller:

```html
<link rel="stylesheet" href="/Free/Scope/get?static=abc123.css">
```
This ensures that when a file changes, the hash changes and the browser loads the new version.

##Passing Data from Controller to View
In the controller, data is passed via the view object's append method:

```php
$this->_view->append([
    'posts' => $posts,
    'title' => 'Post List',
    'count' => count($posts)
]);
```
Inside the phtml file, the passed data becomes available as regular variables with names matching the associative array keys:

```php
<?php foreach ($posts as $post) { ?>
    <h2><?= $post->getPostTitle() ?></h2>
    <p><?= $post->getPostText() ?></p>
<?php } ?>
```

##View Working Recommendations
The View object passes its instance to the phtml view file. All its methods are available immediately, without declaring new instances. This means that $this is available in any phtml file, referencing the current view object.

For IDE convenience, it is recommended to specify variable types via PHPDoc:

```php
/** @var \Modules\Blog\Models\Post[] $posts */
/** @var \Application\Assistance\View\View $this */

$this->addEnvironment();
```
This allows the IDE to suggest methods for $this and type-hint variables passed from the controller.

##Features
CSS and JS files are not cached by the browser - when a file changes, the hash changes, and the browser loads the new version.

Style and script file names must match controller action names when using automatic inclusion via addEnvironment().

When creating a new project, the corresponding folders in Views, Public/css, and Public/js must be created for each module used in the project.

##What's next?
- ➡️ [Section 23. JS Library Choice](/docs/23-js)
- ➡️ [Section 24. Dynamic Signature](/docs/24-security-sign)
- ➡️ [Section 25. Access Protection](/docs/25-security-access)
