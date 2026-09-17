<small>Previous articles:

- ➡️ [Section 03. Quick Start](/docs/03-quick-start)
- ➡️ [Section 04. Philosophy](/docs/04-philosophy)
- ➡️ [Section 05. Principles of Operation](/docs/05-principles)
</small>

# SLOT-H: Architecture

The system is built on a modular principle with clear separation of responsibilities between components. At its core lies the kernel, which contains no business logic and knows nothing about the existence of modules. Modules are layered on top of the kernel and use its capabilities to solve specific tasks.

---

## General scheme

A request arrives at `index.php` - the single entry point. `Router` initializes the `Request` object, which parses the URL and determines the module, controller, and action. Then the module's `Bootstrap` loads, `Preloader` runs to check access rights, and the controller is called. The controller works with models via `DbTables`, and the result is passed to `View` for rendering. The entire process ends with HTML or JSON output.

---

## System layers

**Core** - the foundation on which everything else is built. It provides routing, autoloading, database operations, ORM, templating, caching, and queues. The core contains no business logic and does not depend on modules.

**Modules** - extensions built on top of the core. Each module solves a specific task and has a uniform structure: `Bootstrap`, `Crud`, `Preloader`, `Controllers`, `Models`, `Views`, `Public`. Modules can be added, removed, or replaced without changing the core. The only exception is the `Base` module, which is an integral part of the system.

**Helpers** - utilities used by modules for common operations: working with dates, files, geodata, strings, API requests. Helpers contain no business logic and do not depend on modules.

**Configuration** - three levels of parametrization: global `config.php`, project configuration in ini files, dynamic constants in the `constants` table. Each level complements the previous one rather than overriding it.

---

## Component interaction

The core provides tools, modules use them. Modules do not depend on each other and can work in isolation. Helpers are called from modules as needed. Configuration defines system behavior at all levels.

---

## Request lifecycle

1. Request arrives at `index.php`
2. `Router` initializes `Request`, determines module, controller, action
3. Module's `Bootstrap` loads
4. `Preloader` checks authentication and access rights
5. Controller processes the request, using models via `DbTables`
6. `View` renders the template from the `Views` folder
7. Result is returned to the user

---

## Multi-project

The system supports an unlimited number of projects. Each project is identified by domain or IP address, has its own ini file and log folder. Project data is isolated via the `project_id` field in all tables. All projects are served by a single code instance.

---

## Core and modules

The core knows nothing about modules. It provides APIs for handling requests, databases, and templates. Modules are loaded automatically when a folder exists in the `Modules` directory. The `Base` module is mandatory and cannot be removed.

---

## Summary

The architecture is built on principles of minimalism, predictability, and separation of responsibilities. The core provides tools, modules use them to solve tasks, helpers simplify common operations, and configuration controls behavior. This allows the system to evolve without rewriting existing code.

---

## What's next?

- ➡️ [Section 07. Autoloader](/docs/07-autoloader)
- ➡️ [Section 08. Database Storage Convention](/docs/08-database)
- ➡️ [Section 09. Parametrization](/docs/09-parametrization)