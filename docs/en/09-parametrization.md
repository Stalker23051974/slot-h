<small>Previous articles:

- ➡️ [Section 06. Architecture](/docs/06-architecture)
- ➡️ [Section 07. Autoloader](/docs/07-autoloader)
- ➡️ [Section 08. Database Storage Convention](/docs/08-database)
</small>

# SLOT-H: Parametrization

The system uses a three-level parametrization that allows flexible management of project settings without changing code.

---

## Level 1 - Global configuration

The `Config/config.php` file contains basic settings common to all projects.

This file defines:

- Paths to file storage folders (`Storage`, `Images`, `Logs`, `Dump`)
- Global parameters (`ROOT`, `DEBUG`, `SHOW_QUERIES`)
- Security settings (`IGNORE_SIGN`, `CRYPTO_PROTOCOL`)
- Performance parameters (`AUTOLOAD_MODE`, `memory_limit`)

These settings are the foundation - a dogma that is not overridden but can be extended at the following levels.

---

## Level 2 - Project configuration

Each project has its own ini file in the `Config/Domens` folder.

The file name corresponds to the site address (`myproject.com.ini`, `127.0.0.1.ini`).

This file configures:

- Database connections (`db_host`, `db_name`, `db_login`, `db_password`, `db_engine`)
- Basic project parameters (`default_module`, `default_controller`, `default_action`)
- Default language and currency
- Core modules and controllers

These settings are an axiom for a specific project - they complement the global configuration and define the project's overall behavior.

---

## Level 3 - Dynamic constants

The `constants` table in the database stores parameters that can be changed through the admin interface without restarting the server.

This table stores:

- Security settings (`auth_timeout`, `max_attempt_allow`, `session_lifetime`)
- Password parameters (`min_password_length`, `password_has_uppercase`, `password_has_lowercase`)
- System parameters (`role_superadmin`, `online_timeout`)

These parameters customize site behavior, allowing fine-tuning for specific tasks. Constants can be for a specific project (`project_id` field filled) or for all projects at once (`project_id = NULL`).

This rule is the final setting that determines the specific value of a parameter at any given time.

---

## Level interaction

The levels do not overlap or override each other. They complement each other as:

- **Dogma** - global configuration sets the basic principles of system operation
- **Axiom** - project configuration refines them for a specific site
- **Rule** - dynamic constants determine the final parameter values for a specific situation

Each level is responsible for its own area and does not interfere with other levels.

---

## Where to store what

- In `config.php` - settings that should not change between projects and do not require frequent changes
- In ini files - settings that differ between projects
- In the `constants` table - settings that should be changed through the admin interface without server access

This separation allows managing the project at different levels: from global system settings to specific site parameters.

---

## What's next?

- ➡️ [Section 10. Multi-project](/docs/10-multiproject)
- ➡️ [Section 11. Base Module](/docs/11-module-base)
- ➡️ [Section 12. Free Module](/docs/12-module-free)