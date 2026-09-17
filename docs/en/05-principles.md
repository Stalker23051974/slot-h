<small>Previous articles:

- ➡️ [Section 02. Requirements](/docs/02-requirements)
- ➡️ [Section 03. Quick Start](/docs/03-quick-start)
- ➡️ [Section 04. Philosophy](/docs/04-philosophy)
</small>

# SLOT-H: Principles of Operation

## Core and Modules

---

### Core

The core is the foundation of the system. It handles:

- Request routing (Router, Request)
- Class autoloading (Autoloader)
- Database operations (Database, DbEngine, Select)
- Base ORM (Model)
- Templating (View)
- Controllers (Controller, ApiController, CliController)
- Configuration (Config, CC)
- Caching (CacheAbstract)
- Queues (Queue)

The core does not know which modules exist. It provides tools but contains no business logic.

The core resides in the `Application/Assistance` folder.

---

### Modules

Modules are extensions built on top of the core. Each module solves a specific problem.

**Module structure:**
```text
Modules/
└── ModuleName/
├── Bootstrap.php # Module loader
├── Crud.php # Access rights
├── Preloader.php # Preloading (authentication)
├── Controllers/ # Controllers
├── Models/ # Data models
│ └── DbTables/ # Table definitions
├── Public/ # CSS, JS
│ ├── css/
│ │ └── Project_1/ # Styles for project 1
│ └── js/
│ └── Project_1/ # Scripts for project 1
└── Views/ # Templates
└── Project_1/ # Templates for project 1
```

Modules can be added, removed, or replaced without changing the core. The only exception is the Base module - it is an integral part of the ecosystem and cannot be removed.

---

### How it works

1. Request arrives at `index.php`
2. `Router` initializes the `Request` object and determines which module and controller to call
3. The module's `Bootstrap` loads
4. `Preloader` checks access rights
5. The controller processes the request
6. The model works with the database via `DbTables`
7. `View` renders the template from the `Views` folder

---

### Base modules

- **Base** - users, roles, permissions, queues, logs, constants. An integral part of the system.
- **Free** - public section, main page, static pages.
- **Geo** - countries, cities, languages, currencies, timezones.

New modules follow the same structure and are loaded automatically.

---

### Models

The system follows a paradigm where the controller does not know which model it will work with. This allows full control over the process at any time.

The programmer explicitly calls the required model via a static method within the action code. The controller contains no automatic binding to a model and does not inherit it. The model is loaded only when needed, and only the one required for the specific action.

This allows a single controller to work with different models and makes it easy to swap models without changing the controller.

---

### Comparison with conventional standards

In traditional MVC frameworks (Laravel, Symfony, Yii), the model is usually tightly bound to the controller through constructor declarations or dependency injection. The controller knows which model it works with and calls its methods directly. Replacing the model requires modifying the controller or using complex configuration mechanisms.

In this system, the model is not bound to the controller. The controller knows nothing about which model will be used. The programmer decides which model to call in each specific action.

**Advantages of this approach:**

- The controller is not tied to a specific model. The same controller can work with different models in different actions or projects.
- The model can be overridden for a specific project without modifying the controller.
- Data logic is not duplicated in controllers. It is centralized in models and base classes.
- Testing is simplified, as the model can be swapped at any time.
- Code becomes more readable and predictable - the programmer always sees which model they are working with.

In traditional approaches, overriding a model typically requires creating a new controller or using complex dependency injection mechanisms. In this system, it is solved at the naming convention level.

---

## What's next?

- ➡️ [Section 06. Architecture](/docs/06-architecture)
- ➡️ [Section 07. Autoloader](/docs/07-autoloader)
- ➡️ [Section 08. Database Storage Convention](/docs/08-database)