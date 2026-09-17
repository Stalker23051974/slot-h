<small>Previous articles:

- ➡️ [Section 09. Parametrization](/docs/09-parametrization)
- ➡️ [Section 10. Multi-project](/docs/10-multiproject)
- ➡️ [Section 11. Base Module](/docs/11-module-base)
</small>

# SLOT-H: Free Module

The Free module handles the public-facing part of the site. It is accessible without authentication and contains the pages that users see on their first visit.

---

## Module composition

### Main page

The `Index` controller handles requests to the main page. It is responsible for displaying the project's start page, which is set in the configuration.

### About page

The `About` controller contains information about the project or company. A static page that does not require dynamic content.

### Docs page

The `Docs` controller contains documentation or instructions for users. A static page.

### Static pages

The `Page` controller handles static pages: privacy policy and terms of use.

### Static file serving

The `Scope` controller serves static files: CSS, JavaScript, fonts. Files are served through the controller rather than directly through the web server. This allows control over caching and file versioning. When a file is requested, the system checks its hash and serves the latest version.

### Public API

The `Person` controller provides API methods for working with users in the public section: checking authentication status, retrieving notifications, and session handling.

---

## Absence of models

The Free module does not have a `Models` or `DbTables` folder. This demonstrates that models are an optional part of a module. If a module does not work with its own database tables, the `Models` folder is not created.

This also highlights the architectural principle: controllers do not know about models directly. They call models when necessary, and only those needed for a specific action. The absence of a `Models` folder in the module does not prevent controllers from working with models from other modules, such as `Base`.

---

## Module structure
```text
Modules/Free/
├── Bootstrap.php # Module loader
├── Crud.php # Access rights
├── Preloader.php # Preloading (authentication)
├── Controllers/ # Controllers
│   ├── About.php # About page
│   ├── Docs.php # Documentation page
│   ├── Index.php # Main page
│   ├── Page.php # Static pages (policy, terms)
│   ├── Person.php # Public API
│   └── Scope.php # Static file serving
├── Public/ # CSS, JS
│   ├── css/
│   │   └── Project_1/
│   │       ├── About/
│   │       │   └── index.css
│   │       ├── Docs/
│   │       │   └── index.css
│   │       ├── Index/
│   │       │   └── index.css
│   │       └── Page/
│   │           └── policy_terms.css
│   └── js/
│       └── Project_1/
│           ├── About/
│           │   └── index.js
│           ├── Docs/
│           │   └── index.js
│           └── Index/
│               └── index.js
└── Views/ # Templates
    └── Project_1/
        ├── About/
        │   └── index.phtml
        ├── Docs/
        │   └── index.phtml
        ├── Index/
        │   ├── auth.phtml
        │   └── index.phtml
        └── Page/
            ├── policy.phtml
            └── terms.phtml
```
---

## Access rights

The Free module is accessible without authentication. All module controllers have read access (`READ_RULE`) for all users, including unauthenticated ones. This allows public pages to be displayed without logging in.

---

## Implementation features

- The About and Docs static pages have their own views and styles. The policy and terms pages share the `policy_terms.css` style.
- The `Scope` controller handles static file requests via hash, ensuring files are always up to date. The browser caches files, but when the hash changes, the browser loads the new version.
- The Free module can be extended with additional controllers and views for specific projects. All public pages that do not require authentication should reside in this module.

---

## What's next?

- ➡️ [Section 13. Geo Module](/docs/13-module-geo)
- ➡️ [Section 14. Creating a Module](/docs/14-module-custom)
- ➡️ [Section 15. Database Structure](/docs/15-database-diagram)