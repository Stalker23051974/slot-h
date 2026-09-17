<small>Previous articles:

- ➡️ [Section 07. Autoloader](/docs/07-autoloader)
- ➡️ [Section 08. Database Storage Convention](/docs/08-database)
- ➡️ [Section 09. Parametrization](/docs/09-parametrization)
</small>

# SLOT-H: Multi-project

The system is designed from the ground up to work with an unlimited number of projects. Each project is a separate site with its own configuration, users, and data.

---

## How it works

Each project is identified by the `project_id` field in all tables where data isolation is required. This allows data from different projects to be stored in a single database while remaining isolated from each other.

When querying the database, the ORM automatically adds a condition on `project_id` if the table class inherits from `DatabaseNormalProject` or `DatabaseExtendProject`. This ensures that users only see data from their own project.

---

## Creating a new project

To create a new project, you need to:

1. Add a record to the `projects` table. The `project_id` field is the primary key and project identifier.

2. Create an ini file in the `Config/Domens` folder. The file name must match the project's web address. If access is via a port, the colon in the name is replaced with a dot.

   Example: `127.0.0.1:81` → `127.0.0.1.81.ini`

3. Create a log folder in `Config/Logs` with the name matching the ini file name, and assign permissions for this folder to the `www-data` user.

4. Create the folder `Modules/Free/Views/Project_<project_number>/Index` and place an `index.phtml` file inside it. This will correspond to the `index` action in the `Modules/Free/Index` controller.

The `Free` module, `Index` controller, and `index` action are set in the ini file as the project's start page and can be overridden in the configuration.

After completing these steps, the project becomes accessible at the specified address.

---

## Project identification

The project is identified by the domain (or IP address with port) from which the request came. This parameter determines the ini file. The system then verifies the correspondence between the request domain and the project identifier in the database and ini file.

This ensures that the project is only accessible at the address specified during its creation.

---

## Data isolation

Data from different projects is isolated at the database level. The `project_id` field is present in all tables where data should be separated by project.

Tables without `project_id` contain shared data accessible to all projects. For example, tables with geodata (countries, cities, languages) are shared across the entire system.

---

## Users and projects

In the demo version of the system, each user is tied to a single project. In the full version, two additional scenarios are available:

- **Access to external projects** - an administrator can access the admin panels of different projects with a single account.
- **Account linking across projects** - user accounts from different projects can be linked together.

---

## Project configuration

Each project has its own configuration file. It specifies parameters specific to that project:

- Database connections (each project can use its own database)
- Module, controller, and action for the main page
- Default language, currency, and timezone
- Additional parameters overriding global settings

This allows running projects with different settings on a single system.

---

## Virtual hosts

Each project requires a virtual host configuration on the server. The system does not create them automatically - this is the server administrator's responsibility.

All projects are served by a single code instance (the `index.php` entry point), and the differences between them are achieved through configuration and data isolation.

---

## Scalability

Because projects are only isolated at the data level, the system can serve any number of projects without code changes.

The only limitation is server and database performance. As the number of projects grows, resource increases or separate databases for large projects may be required.

---

## What's next?

- ➡️ [Section 11. Base Module](/docs/11-module-base)
- ➡️ [Section 12. Free Module](/docs/12-module-free)
- ➡️ [Section 13. Geo Module](/docs/13-module-geo)