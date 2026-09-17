<small>Previous articles:

- ➡️ [Section 14. Creating a Module](/docs/14-module-custom)
- ➡️ [Section 15. Database Structure](/docs/15-database-diagram)
- ➡️ [Section 16. ORM Principles](/docs/16-orm-principles)
</small>

# SLOT-H: Working Methods

The ORM provides a set of predefined methods for working with data, as well as allowing you to create custom search methods in DbTables classes.

---

## Predefined Methods

### getRow

Retrieves a single record by primary key or multiple records by an array of identifiers.

```php
$record = Table::getRow($id);
$records = Table::getRow([1, 2, 3]);
```
The method returns a model if a single identifier is passed, or an array of models if an array of identifiers is passed.

###getAll
Retrieves all records from the table.

```php
$all = Table::getAll();
```
Supports pagination and sorting:

```php
$page = 2; // second page
$order = [Table::FIELD_NAME => true]; // true - ASC, false - DESC
$records = Table::getAll($page, $order);
```
##getCount
Retrieves the number of records.

```php
$count = Table::getCount();
```
Accepts conditions for counting:

```php
$count = Table::getCount([Table::FIELD_STATUS => 1]);
```
##getByCondition
Retrieves records based on conditions set via the Select object.

```php
$records = Table::getByCondition();
```
This method is used internally but can be called directly to get results after building a query.

##remove
Deletes records based on a condition.

```php
Table::remove([Table::FIELD_STATUS => 0]);
```
For tables with soft delete (DatabaseExtend), the method sets deleted_at instead of physically deleting.

##getByName
Retrieves a record by name. The DbTable class must define the $_name property for this method to work.

```php
$record = Table::getByName('John');
```

##save
Saves a record. Accepts an array of fields and returns the ID of the saved record.

```php
$id = Table::save([Table::FIELD_NAME => 'John', Table::FIELD_STATUS => 1]);
```
For tables with updated_at (DatabaseExtend), the field is updated automatically.

##Creating Custom Search Methods
In addition to predefined methods, DbTables classes can define custom methods for specific queries. Use getSelect() to return a Select object for building the query.

###Example from the Base module:
```php
public static function getByUid($label, $project = CURRENT_PROJECT)
{
    return (self::getSelect())
        ->addWhere([
            self::createWhere(self::PERSON_PRIVATE_UID, $label),
            self::createWhere(self::PROJECT_ID, $project)
        ])
        ->pop()
        ->result();
}
```
Example breakdown:

self::getSelect() - creates a new Select object for the current table.

addWhere() - adds conditions to the query. Accepts an array of conditions created via createWhere.

createWhere() - creates a condition object for a field. Field constants are used instead of string field names.

pop() - specifies that only one record should be returned instead of an array.

result() - executes the query and returns the result.

Important: In all methods, field constants defined in the DbTable class should be used instead of string field names. This ensures consistency and simplifies refactoring.

###Other Examples
Search with multiple conditions using LIKE:
```php
public static function getBySearch($search)
{
    return (self::getSelect())
        ->addWhere([
            self::createWhere(self::FIELD_NAME, $search, Where::LIKE),
            self::createWhere(self::FIELD_STATUS, 1)
        ])
        ->result();
}
```
Search with sorting:
```php
public static function getActiveSorted()
{
    return (self::getSelect())
        ->addWhere(self::createWhere(self::FIELD_STATUS, 1))
        ->order(self::FIELD_SORT)
        ->result();
}
```
Search with pagination:
```php
public static function getByPage($page)
{
    return (self::getSelect())
        ->addWhere(self::createWhere(self::FIELD_STATUS, 1))
        ->page($page)
        ->result();
}
```
Search returning an array instead of a model:
```php
public static function getListAsArray()
{
    self::setModelResponse(false);
    return (self::getSelect())
        ->addWhere(self::createWhere(self::FIELD_STATUS, 1))
        ->result();
}
```
##Working with the Select Object
The getRow, getAll, and getByCondition methods use the Select object to build queries. You can explicitly retrieve a Select object via getSelect:

```php
$select = Table::getSelect();
$select->addWhere(Table::createWhere(Table::FIELD_STATUS, 1));
$records = $select->result();
```
##Method Chaining
Methods support chaining for building queries:

```php
$records = Table::getSelect()
    ->addWhere(Table::createWhere(Table::FIELD_STATUS, 1))
    ->order(Table::FIELD_NAME)
    ->page(2)
    ->result();
```
##Caching
Methods support caching via the $cache parameter:

```php
$record = Table::getRow($id, false, false, true, true);  // with cache
$record = Table::getRow($id, false, false, true, false); // without cache
```
##Operation Modes
The mode management methods allow temporarily changing ORM behavior for the current query. After query execution, all flags are automatically reset to their default state.

This ensures that changing the operation mode for one query does not affect subsequent operations.

###setModelResponse
Controls the return format of data:

true - models are returned

false - arrays are returned

```php
Table::setModelResponse(false);
$records = Table::getAll(); // returns arrays
```
After query execution, the mode returns to the default value (true).

###setIgnoreExtend
Disables deleted_at filtering for tables with soft delete. Allows retrieving deleted records.

```php
Table::setIgnoreExtend(true);
$records = Table::getAll(); // includes deleted records
```
After query execution, the flag resets to false.

###setIgnoreProject
Disables project_id filtering. Allows retrieving records from all projects.

```php
Table::setIgnoreProject(true);
$records = Table::getAll(); // includes records from all projects
```
After query execution, the flag resets to false.

##What's next?

- ➡️ [Section 18. Using JOINs](/docs/18-join)
- ➡️ [Section 19. Models](/docs/19-models)
- ➡️ [Section 20. JSON Data](/docs/20-orm-json)
