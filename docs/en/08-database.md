<small>Previous articles:

- ➡️ [Section 05. Principles of Operation](/docs/05-principles)
- ➡️ [Section 06. Architecture](/docs/06-architecture)
- ➡️ [Section 07. Autoloader](/docs/07-autoloader)
</small>

# SLOT-H: Database Storage Convention

The system uses strict naming conventions that make the data structure predictable and uniform.

---

## Class naming

The model class and DbTable class must have the **same name**.

Example: `Person` model and `Person` DbTable

The class name is written in singular form, in CamelCase. This implies that the result of the model is a single object, even if the method returns a collection.

---

## Table naming

The table name in the storage is written in **lowercase plural**.

This implies that the table stores multiple entities.

Example: `persons` table for the `Person` model

---

## Field naming

Field names in the storage always contain a prefix - the entity name in singular, followed by an underscore and the value name.

Example: `person_id`, `person_first_name`, `person_second_name`

### Foreign key fields

If a field references another table, the column name must match the primary key name in that table.

Example: In the `persons` table, the `country_id` field references the `countries` table, where the primary key is named `country_id`

---

## Complete structure example

- **Model:** `Person`
- **DbTable:** `Person`
- **Database table:** `persons`
- **Fields:** `person_id`, `person_first_name`, `person_second_name`, `country_id`, `city_name`, `language_id`
- **Relationship:** `country_id` references `countries.country_id`
- **Relationship:** `language_id` references `languages.language_id`

---

## Why this is needed

**Uniformity.** Any developer opening the project immediately understands the data structure without studying documentation.

**Predictability.** The table name always corresponds to the model name in plural. The field name always corresponds to the entity name in singular.

**Automation.** The ORM base classes use these conventions to automatically build queries and relationships between tables.

**Relationships.** By the name of a field referencing another table, the ORM determines the relationship and automatically builds JOIN queries.

---

## What's next?

- ➡️ [Section 09. Parametrization](/docs/09-parametrization)
- ➡️ [Section 10. Multi-project](/docs/10-multiproject)
- ➡️ [Section 11. Base Module](/docs/11-module-base)