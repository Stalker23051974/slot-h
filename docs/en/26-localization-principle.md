<small>Previous articles:

- ➡️ [Section 23. JS Library Choice](/docs/23-js)
- ➡️ [Section 24. Dynamic Signature](/docs/24-security-sign)
- ➡️ [Section 25. Access Protection](/docs/25-security-access)
</small>

# SLOT-H: Localization Principles

Localization in the system is divided into three types depending on the text source and translation method. All work on a single principle: if a translation exists for the user's current language, it is displayed; if not, the original text is shown.

---

## 1. Text in Code

Texts hardcoded in PHP files are passed through the static method `\Config\CC::locale('....')`.

**Example:**

```php
echo CC::locale('Welcome');
```
For such texts, there is a collector that scans the code, finds all CC::locale() calls, and updates the list of strings for translation. These strings are stored in the tkeys table.

In the full version, an automatic translation mechanism via external service APIs is provided. It generates the initial translation pool in the tvalues table. "Initial" because machine translation requires manual adjustment, as the quality of automatic translation is insufficient for production use.

Translations are performed for all languages enabled in the project (project_languages table).

If a translation is missing, the system returns the original text passed as an argument to CC::locale().

##2. Full Texts
These are texts stored in the texts table and managed through the administrative interface. They use the associated translation table t_texts.

Automated translation is not used for such texts. Large volumes of text produce results that are unacceptable from a native speaker's perspective when machine-translated. Translations are performed manually through the admin interface.

Structure:

texts - main table with original texts

t_texts - translation table linked to texts via the parent field

##3. Translatable Data in Storage
For tables that should support translations, the following property is set in the DbTable class:

```php
public static $_translate = true;
```
To work with translations, you need to:

Create a translation table named t_<main_table_name>

This table must have the following fields:

- language_id - language identifier
- parent - record identifier from the main table
- Add fields corresponding to the translatable fields of the main table (1:1 by names and types)

The programmer does not need to think about JOINs and data. Their task is to work with the original model, without using the model with the t_ prefix. The ORM automatically generates a single query that retrieves the main table data and a JSON containing all translations.

Example demonstrating the principle:

In the admin section, the following is called:

```php
$links = \Modules\Base\Models\DbTables\Alias::getAll();
```
The ORM generates the following SQL query:

```sql
SELECT 
  aliases.*,
  CONCAT(
    "{",
    GROUP_CONCAT(
      CONCAT(
        '"',
        dev.t_aliases.language_id,
        '":{"alias_name":"',
        REPLACE(
          REPLACE(
            REPLACE(
              IFNULL(dev.t_aliases.alias_name,''),
              '"',
              '\\"'
            ),
            '\n',
            '<br>'
          ),
          '\r',
          ''
        ),
        '"}'
      )
    ),
    "}"
  ) AS tblock
FROM 
  `dev`.`aliases` AS aliases
LEFT JOIN dev.t_aliases ON dev.t_aliases.parent = dev.aliases.alias_id
WHERE dev.aliases.alias_id IS NOT NULL
AND aliases.project_id = "1"
GROUP BY dev.aliases.alias_id
```
All translations are collected in the JSON field tblock, which is then parsed by the model. When the getter is called, the model checks for a translation in the current language and returns it if available.

## Single Principle
In all three cases, one rule applies:

If a translation for the user's current language exists - it is displayed. If not - the original text is shown.

The original text for code is the argument of the CC::locale() method. For data - it is the value in the original table.

The programmer works only with the original entities. Once a translation table with the t_ prefix is created and the translatable entity flag is overridden in the class, the programmer can forget about translations. Everything related to JOINs, grouping, and translation substitution is handled by the ORM. This makes the system resilient to missing translations: the user always sees text, even if not in their own language.

## What's next?
- ➡️ [Section 27. Queue Server Startup](/docs/27-queue-start)
- ➡️ [Section 28. Queue Server Tasks](/docs/28-queue-tasks)
- ➡️ [Section 29. Helpers](/docs/29-utils-helpers)
