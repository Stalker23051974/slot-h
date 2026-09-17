<small>Previous articles:

- ➡️ [Section 13. Geo Module](/docs/13-module-geo)
- ➡️ [Section 14. Creating a Module](/docs/14-module-custom)
- ➡️ [Section 15. Database Structure](/docs/15-database-diagram)
</small>

# SLOT-H: ORM Principles

The ORM in the system is built on two key ideas: minimalism and predictability. It does not try to be a universal solution for all tasks, but provides exactly what is needed for working with data within the ecosystem.

---

## Separation into DbTables and Models

The ORM is divided into two layers:

- **DbTables** - table structure description. Fields, types, indexes, relationships. No logic. Only description.
- **Models** - data handling. Getters, setters, relationships, transformations. Object-level logic.

This separation allows:

- Changing table structure without changing data logic.
- Changing data logic without changing table structure.
- Using DbTables without Models (e.g., for simple queries).
- Extending Models without changing DbTables.

---

## Controller and Model

The controller does not know which model it works with. It does not declare a model or inherit from it. The programmer explicitly calls the required model via a static method in the action code.

This provides the ability to:

- Work with different models in a single controller.
- Easily swap models for a specific project.
- Avoid duplicating data logic in controllers.

---

## Static Methods

Models and DbTables use static methods for data operations. This keeps the code simple and clear:

```php
$post = Post::getRow($id);
$posts = Post::getAll();
```
The static approach does not require instantiating an object to execute queries. The object is created only when the result is obtained.

##Where Object
The Where object describes a single condition in a query. It supports the following condition types:

Simple comparisons: EQUAL, NOT_EQUAL, MORE, LESS, MORE_EQUAL, LESS_EQUAL

NULL checks: IS_NULL, NOT_NULL

Pattern matching: LIKE, LIKE_START, LIKE_END, LIKE_EQUAL and their negations NOT_LIKE, NOT_LIKE_START, NOT_LIKE_END, NOT_LIKE_EQUAL

Regular expressions: REGEXP, NOT_REGEXP

Comma-separated string search: FIND_IN_SET, NOT_FIND_IN_SET

Range: BETWEEN

Batch operations: ARR_EQUAL (IN), ARR_NOT_EQUAL (NOT IN), as well as variants for MORE, LESS, LIKE with AND/OR grouping

##Block Object
The Block object combines multiple Where conditions or other Blocks into a single group. It forms an expression with AND or OR connectors.

This allows building complex conditions:

```sql
(condition1 AND condition2) OR (condition3 AND condition4)
```
##Query Optimization
All WHERE conditions are sorted by weight - from lowest to highest. This ensures that the most efficient conditions using indexes are executed first.

NULL checks have weight 0

Equality (EQUAL) has weight 1

Comparisons (MORE, LESS, NOT_EQUAL) have weight 2

IN and NOT IN have weight 3

LIKE and regular expressions have weight 4-5

Batch operations (ARR_*) have weight 8-10

When building a query, conditions with lower weight are executed first, speeding up query execution.

##Database Base Classes
All database classes inherit from Application/Assistance/Database.

Database - base class. Contains core data logic: query building, execution, result processing.

DatabaseCache - extends the base class with caching. Query results are stored in static cache or external storage.

DatabaseExtend - adds deleted_at and updated_at fields. Automatically adds deleted_at IS NULL to all queries (soft delete). The updated_at field is updated automatically on save.

DatabaseExtendProject - adds the project_id field. Automatically adds a condition to all queries for the current project. The project is determined by the request domain.

DatabaseExtendStatus - adds status filtering. Automatically adds a condition based on the object's status list, if defined.

DatabaseNormal - direct inheritance from Database without additional fields or conditions.

DatabaseNormalProject - similar to DatabaseExtendProject, but without deleted_at and updated_at fields.

DatabaseNormalStatus - similar to DatabaseExtendStatus, but without deleted_at and updated_at fields.

Inheritance Hierarchy
```text
Database
├── DatabaseNormal
│   ├── DatabaseNormalProject
│   └── DatabaseNormalStatus
├── DatabaseExtend
│   ├── DatabaseExtendProject
│   └── DatabaseExtendStatus
└── DatabaseCache
```
The class choice depends on what fields and filtering are needed for the table:

No soft delete or project isolation - DatabaseNormal

Soft delete required - DatabaseExtend

Project isolation required - DatabaseNormalProject or DatabaseExtendProject

Status filtering required - DatabaseNormalStatus or DatabaseExtendStatus

##Automatic Relationships
By field name, the ORM determines the relationship with another table. If the field is named country_id, the system assumes it references the countries table and the country_id field. This works via FP_LINK in the field description.

For relationships in models, the following methods are used:

linkField() - retrieves the related object (one-to-one)

lotsFieldInModuleByField() - retrieves a collection of related objects (one-to-many)

relatedFieldInModuleByField() - retrieves the reverse relationship (belongs-to)

All of this is automatically generated based on the field descriptions in DbTables.

##Minimalism in Models
Models contain no logic. They only declare properties and inherit from the base class. All getters, setters, and relationship methods live in the base Model class.

This makes models as short and clear as possible.

Working with Data via Select
Queries are built through the Select object, which assembles the SQL query piece by piece. This allows:

Building queries of any complexity

Adding conditions via Where and Block

Using aggregate functions (COUNT, MAX, MIN, SUM)

Managing caching at the query level

Select does not execute the query on its own. It is passed to DbEngine, which works with the specific DBMS. The engine type is specified in the project's ini file via the db_engine parameter. This allows a single core to serve multiple sites simultaneously, each potentially using a different DBMS (MySQL, PostgreSQL, MongoDB). The only limitation is server performance.

##Field Typing
Each field in DbTables is described with a type. This allows:

Casting values to the correct type when setting

Formatting values when retrieving

Optimizing queries based on field type

Main types: INT, FLOAT, STRING, TEXT, DATE, DATETIME, JSON, BOOLEAN.

In the DbTables class, each field is defined in the $_fields property as follows (from the earlier example):

```php
public static $_fields = [
    self::POST_ID => [],
    self::POST_TEXT => [self::FP_TYPE => self::TYPE_TEXT],
    self::POST_AUTHOR => [self::FP_TYPE => self::TYPE_STRING],
    self::POST_TIME => [],
];
```
The key is the field name, the value is an array for optional overrides.

The full list of values passed to the ORM for each field:

```php
/** @var array Default field type template */
protected $_typeTemplate = [
    db::FP_TYPE => db::TYPE_INT,
    db::FP_NULL => false,
    db::FP_TRIM => true,
    db::FP_DEFAULT => 0,
    db::FP_INDEX => false,
    db::FP_CACHE => false,
    db::FP_LENGTH => null,
];
```
This means that to declare a numeric, non-nullable field, an empty array is sufficient:

```php
self::POST_TIME => []
```
To define a field as a string, simply change its type:

```php
self::POST_AUTHOR => [self::FP_TYPE => self::TYPE_STRING]
```
##Caching
The ORM supports two levels of caching:

Static cache at the query level. Query results are stored for the duration of the script execution.

External cache via DatabaseCache. Results are stored in external storage (Redis, Memcached) for reuse across requests.

Caching is controlled via query parameters and does not require changes to data logic.

##Project Isolation
If a table inherits from DatabaseNormalProject or DatabaseExtendProject, the ORM automatically adds a project_id condition to all queries. This ensures users only see data from their own project.

To disable project isolation, use setIgnoreProject() and getIgnoreProject().

##Soft Delete
If a table inherits from DatabaseExtend, the ORM automatically adds a deleted_at IS NULL condition to all queries. This implements soft delete.

To disable soft delete, use setIgnoreExtend().

##Summary
The ORM in this system is not a heavyweight framework. It is a set of conventions and base classes that allow working with data quickly, predictably, and without unnecessary code. It does not hide SQL - it provides tools for building it. It does not impose architecture - it suggests a path.

##What's next?
- ➡️ [Section 17. Working Methods](/docs/17-orm-methods)
- ➡️ [Section 18. Using JOINs](/docs/18-join)
- ➡️ [Section 19. Models](/docs/19-models)
