<small>Previous articles:

- ➡️ [Section 08. Database Storage Convention](/docs/08-database)
- ➡️ [Section 09. Parametrization](/docs/09-parametrization)
- ➡️ [Section 10. Multi-project](/docs/10-multiproject)
</small>

# SLOT-H: Base Module

The Base module is the core of the ecosystem and an integral part of the system. It cannot be removed or replaced, as it provides the basic functionality required for any project to work.

---

## Module composition

The Base module includes the following components:

### Users and authentication

User management, registration, login, and password recovery. Storage of user data, roles, and permissions.

### Roles and permissions

A hierarchical role system with permission inheritance. Each role can have a parent role from which it inherits permissions. Permissions are assigned to both roles and individual users.

### Task queues

A background task system for asynchronous processing. Supports email sending, image processing, database dumps, and other task types. The queue runs via a CLI worker launched by cron.

### Logging

Logging of user actions and system events. Logs include login attempts, page views, data changes, record creation, and deletion.

### Constants

Dynamic constants stored in the database. Allow changing system settings without editing files or restarting the server.

### Short links

Management of short URLs for project pages. Allows creating human-readable addresses instead of standard `module/controller/action` paths.

### Files and images

Management of uploaded files and images. Supports image processing: resizing, thumbnail creation, and optimization.

### Static files

Version management for CSS and JS files through hashing. Automatic version updates when files change to clear browser cache.

### Notifications

A user notification system for events. Supports various notification types, including system alerts.

---

## Base module tables

- `aliases` - short links to pages
- `auth_checks` - authentication attempts
- `brutus` - unauthorized access attempts
- `constants` - system and user constants
- `entities` - unique entity identifiers
- `icons` - SVG icons
- `person_actives` - user online status
- `person_alerts` - user notifications
- `person_logs` - user action logs
- `person_privates` - user authentication data
- `person_protecteds` - additional user data
- `person_roles` - user-role relationships
- `person_rules` - user-permission relationships
- `persons` - users
- `project_languages` - project languages
- `projects` - projects
- `queues` - queue tasks
- `role_roles` - role hierarchy
- `role_rules` - role permissions
- `roles` - roles
- `rules` - permissions
- `static_files` - static file hashes
- `t_texts` - text translations
- `texts` - static texts
- `tkeys` - translation keys
- `tvalues` - translation values

---

## Module structure
```text
Modules/Base/
├── Bootstrap.php # Module loader
├── Crud.php # Access rights
├── Preloader.php # Preloading
├── Controllers/ # Controllers
│ └── Cli.php # CLI controller (queues, daemon)
├── Models/ # Data models
│ ├── DbTables/ # Table definitions
│ │ ├── Alias.php
│ │ ├── Person.php
│ │ ├── Role.php
│ │ ├── Queue.php
│ │ └── ...
│ └── (models)
```
---

## Features

The Base module has no public-facing interface of its own. Its functionality is used by other modules through calls to models and controllers.

The Base module's CLI controller runs background tasks and manages the queue. It is not intended to be called via the web interface.

All Base module tables requiring data isolation use `project_id` for project separation.

The Base module is loaded automatically during system initialization and requires no additional configuration. All its components are available through standard kernel mechanisms.

---

## What's next?

- ➡️ [Section 12. Free Module](/docs/12-module-free)
- ➡️ [Section 13. Geo Module](/docs/13-module-geo)
- ➡️ [Section 14. Creating a Module](/docs/14-module-custom)