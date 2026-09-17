<small>Previous articles:

- ➡️ [Section 12. Free Module](/docs/12-module-free)
- ➡️ [Section 13. Geo Module](/docs/13-module-geo)
- ➡️ [Section 14. Creating a Module](/docs/14-module-custom)
</small>

# SLOT-H: Database Structure

The demo version includes tables that provide full system functionality.

---

## aliases

Table for storing short links. Allows creating human-readable URLs instead of standard `module/controller/action` paths. Each record maps a short path to a full internal address.

- `alias_id` - primary key
- `alias_link` - full internal path (e.g., `Free/About/index`)
- `alias_value` - short path (e.g., `about`)
- `alias_name` - display name in admin panel
- `alias_type` - link type (visible, hidden)
- `project_id` - project identifier

---

## auth_checks

Table for tracking authentication attempts. Used for brute force protection - limits the number of failed login attempts from a single client.

- `auth_check_id` - primary key
- `auth_check_time` - attempt timestamp
- `auth_check_hash` - client hash (IP + User-Agent)
- `auth_check_attempt` - number of attempts

---

## brutus

Table for logging suspicious requests. Logs attempts to access non-existent pages and files, helps detect system scanning.

- `brutus_id` - primary key
- `brutus_url` - requested URL
- `brutus_counter` - number of requests to this URL

---

## constants

Table for storing dynamic constants. Allows changing system settings through the admin interface without editing files or restarting the server. Constants can be shared across all projects or tied to a specific project.

- `constant_id` - primary key
- `constant_value` - constant value
- `constant_name` - constant name (identifier)
- `constant_type` - type (system or user)
- `constant_description` - description
- `constant_data` - additional data (JSON)
- `constant_group` - constant group
- `project_id` - project identifier (NULL for all projects)

---

## entities

Table for storing relationships between entities and their values. Used for universal references and searching.

- `entity_id` - primary key
- `entity_entity` - entity identifier
- `entity_entity_type` - entity type
- `entity_value` - value

---

## icons

Table for storing SVG icons. Stores paths to icons available through the system.

- `icon_id` - primary key
- `icon_path` - SVG path
- `icon_constant` - icon constant
- `icon_frontend` - frontend availability

---

## languages

Table for storing languages. Contains information about supported languages, their ISO codes, writing direction (LTR/RTL), and voice settings.

- `language_id` - primary key
- `language_name` - language name
- `language_iso_1` - two-letter ISO code
- `language_iso_3` - three-letter ISO code
- `language_status` - status (enabled/disabled)
- `language_privacy_url` - privacy policy URL
- `language_license_url` - license URL
- `language_flag` - language flag
- `language_direction` - writing direction (LTR/RTL)
- `language_voice_male` - male voice
- `language_voice_female` - female voice
- `language_s` - language suffix
- `updated_at` - update time
- `deleted_at` - deletion time (soft delete)

---

## person_actives

Table for storing information about active users. Tracks who is currently on the site and is used to display online status.

- `person_active_id` - primary key
- `person_id` - user identifier
- `person_active_date` - last activity time
- `person_active_session` - session identifier

---

## person_alerts

Table for storing user notifications. Used for sending system messages and alerts.

- `person_alert_id` - primary key
- `person_id` - user identifier
- `person_alert_type` - notification type
- `person_alert_time` - creation time
- `person_alert_data` - notification data (JSON)

---

## person_logs

Table for logging user actions. Records all actions: login, page views, data changes, record creation and deletion. Used for auditing and activity analysis.

- `person_log_id` - primary key
- `person_id` - user identifier
- `person_log_name` - user name
- `person_log_ip` - IP address
- `person_log_time` - action time
- `person_log_module_code` - module code
- `person_log_controller_code` - controller code
- `person_log_module` - module name
- `person_log_controller` - controller name
- `person_log_action` - action name
- `person_log_entity` - entity identifier
- `person_log_change` - change description
- `person_log_archive` - archive flag

---

## person_privates

Table for storing user authentication data. Contains logins, password hashes, and UIDs for sessions.

- `person_private_id` - primary key
- `person_id` - user identifier
- `person_private_banned` - ban status
- `person_private_hash` - password hash
- `person_private_uid` - unique session identifier
- `person_private_login` - user login
- `project_id` - project identifier

---

## person_protecteds

Table for storing additional user data. Contains settings and preferences in JSON format.

- `person_protected_id` - primary key
- `person_id` - user identifier
- `person_protected_data` - user data (JSON)

---

## person_roles

Table for linking users to roles. Determines which roles are assigned to each user.

- `person_role_id` - primary key
- `person_id` - user identifier
- `role_id` - role identifier

---

## person_rules

Table for individual user permissions. The main permission set comes from `role_rules` via the user's role from `person_roles`. This table allows custom assignment of additional permissions or disabling base permissions from the role.

- `person_rule_id` - primary key
- `person_id` - user identifier
- `rule_id` - permission identifier
- `person_rule_action` - action (add/revoke)

---

## persons

Main user table. Stores personal data: first name, last name, patronymic, age, gender, status, language and timezone settings.

- `person_id` - primary key
- `person_first_name` - first name
- `person_second_name` - last name
- `person_patronymic` - patronymic
- `country_id` - country identifier
- `city_name` - city name
- `person_age` - date of birth
- `person_male` - gender
- `person_status` - user status
- `project_id` - project identifier
- `parent` - parent user identifier
- `language_id` - language identifier
- `voice_id` - voice identifier
- `person_notification` - notification settings
- `timezone_id` - timezone identifier
- `updated_at` - update time
- `deleted_at` - deletion time (soft delete)

---

## project_languages

Table for linking projects to languages. Determines which languages are available in each project and their display order.

- `project_language_id` - primary key
- `project_id` - project identifier
- `language_id` - language identifier
- `project_language_order` - sort order

---

## projects

Main project table. Stores information about each site: name, domain, timezone, and additional data.

- `project_id` - primary key
- `project_name` - project name
- `project_landing` - project domain
- `timezone_id` - timezone identifier
- `project_data` - additional data (JSON)
- `updated_at` - update time
- `deleted_at` - deletion time (soft delete)

---

## queues

Table for storing asynchronous queue tasks. Used for background processing: email sending, image processing, dump creation.

- `queue_id` - primary key
- `queue_status` - task status
- `queue_params` - task parameters (JSON)
- `queue_action` - task type
- `queue_time` - execution time
- `queue_first_start` - first start time
- `queue_process_id` - process identifier

---

## role_roles

Table for building role hierarchy. Determines which role is the parent of another.

- `role_role_id` - primary key
- `parent` - parent role identifier
- `role_id` - child role identifier

---

## role_rules

Table for linking roles to permissions. Determines which permissions are available for each role.

- `role_rule_id` - primary key
- `role_id` - role identifier
- `rule_id` - permission identifier

---

## roles

Roles table. Stores information about user roles and their hierarchy.

- `role_id` - primary key
- `role_name` - role name
- `parent` - parent role identifier
- `role_data` - additional data (JSON)
- `project_id` - project identifier
- `role_internal` - internal role flag
- `updated_at` - update time
- `deleted_at` - deletion time (soft delete)

---

## rules

Permissions table. Stores the list of available permissions, grouped by categories.

- `rule_id` - primary key
- `rule_name` - permission name
- `rule_group` - permission group
- `rule_subgroup` - permission subgroup
- `project_id` - project identifier

---

## static_files

Table for storing static file hashes (CSS, JS). Used for cache management and automatic version updates.

- `static_file_id` - primary key
- `static_file_path` - file path
- `static_file_hash` - file hash
- `static_file_time` - last modification time

---

## t_languages

Translation table for languages. Stores localized language names.

- `t_language_id` - primary key
- `language_id` - language identifier
- `parent` - parent record identifier
- `language_name` - language name in target language

---

## t_texts

Translation table for texts. Stores localized versions of text blocks.

- `t_text_id` - primary key
- `language_id` - language identifier
- `parent` - parent record identifier
- `text_value` - translated text
- `text_description` - translation description

---

## texts

Table for storing static texts. Used for managing page content through the admin interface.

- `text_id` - primary key
- `text_tag` - unique text tag
- `text_value` - text
- `text_description` - description
- `text_group` - text group
- `project_id` - project identifier
- `form_id` - form identifier
- `text_menu` - menu binding
- `text_menu_enabled` - show in menu
- `text_alternative` - alternative text
- `text_alternative_enabled` - alternative enabled

---

## timezones

Time zone table. Contains a list of available time zones with their offset from UTC.

- `timezone_id` - primary key
- `timezone_standart` - standard name
- `timezone_value` - display value
- `timezone_offset` - offset in hours
- `timezone_code` - timezone code

---

## tkeys

Table for storing translation keys. Contains unique keys for strings used in code.

- `tkey_id` - primary key
- `tkey_key` - original text
- `tkey_hash` - text hash

---

## tvalues

Table for storing translation values. Contains translations for each language.

- `tvalue_id` - primary key
- `tkey_id` - key identifier
- `language_id` - language identifier
- `tvalue_value` - translated value
- `project_id` - project identifier

---

## Relationship Diagram

Below is the relationship diagram between the demo version tables.

![Database Relationship Diagram](../schema.png)

---

## What's next?

- ➡️ [Section 16. ORM Principles](/docs/16-orm-principles)
- ➡️ [Section 17. Working Methods](/docs/17-orm-methods)
- ➡️ [Section 18. Using JOINs](/docs/18-join)