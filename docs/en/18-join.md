<small>Previous articles:

- ➡️ [Section 15. Database Structure](/docs/15-database-diagram)
- ➡️ [Section 16. ORM Principles](/docs/16-orm-principles)
- ➡️ [Section 17. Working Methods](/docs/17-orm-methods)
</small>

---
# Complex Queries

The demo version of the system provides two additional tools for building complex queries: the `JoinField` class for working with JOIN conditions and the `FieldHandler` class for field operations.

---

## JoinField

The `JoinField` class is designed to describe fields used in JOIN conditions. It allows you to specify the database, table, and field name separately, which is useful when working with multiple databases or when table aliases are required.

The class provides a fluent interface for setting properties.

```php
$field = new JoinField()
    ->setBase('main_db')
    ->setTable('users')
    ->setField('user_id');
```
###Methods
|Method|Description|
|------|-----------|
|setBase($base)|Sets the database name|
|setTable($table)|Sets the table name or alias|
|setField($field)|Sets the field name|

##FieldHandler
The FieldHandler class allows you to apply various operations to fields in a SELECT query. It supports aggregate functions, conditional operations, and transformations.

```php
$handler = new FieldHandler()
    ->setBase('main_db')
    ->setTable('users')
    ->setField('created_at')
    ->setIfNull('1970-01-01');
```
###Methods
|Method|Description|
|------|-----------|
|setCount()|Applies COUNT to the field|
|setMin()|Applies MIN to the field|
|setMax()|Applies MAX to the field|
|setIfNull($override)|Replaces NULL with the specified value|
|setReplace($search, $override)|Replaces occurrences of $search with $override|
|setConcat($list)|Concatenates multiple fields into a string|
|setGroupConcat()|Applies GROUP_CONCAT (string aggregation)|
|setAlias($value)|Sets an alias for the field|
|setBase($value)|Sets the database name|
|setTable($value)|Sets the table name or alias|
|setField($value)|Sets the field name|
|setFlag($value)|Sets an additional flag|
####Example: JOIN with Multiple Conditions
In this example, JoinField is used to build a JOIN condition between the languages and project_languages tables, filtered by project_id.

```php
public static function getActive()
{
    return (static::getSelect())
        ->addWhere(
            static::createWhere(
                // Main field
                (new JoinField())
                    ->setBase(self::getBase())
                    ->setTable(self::$_table)
                    ->setField(self::LANGUAGE_ID),
                null,
                Where::NOT_NULL,
                [PL::getBase(), PL::$_table],
                [
                    // Join condition: language_id = language_id
                    static::createWhere(
                        (new JoinField())
                            ->setBase(PL::getBase())
                            ->setTable(PL::$_table)
                            ->setField(PL::LANGUAGE_ID),
                        (new JoinField())
                            ->setBase(self::getBase())
                            ->setTable(self::$_table)
                            ->setField(self::LANGUAGE_ID)
                    ),
                    // Filter condition: project_id = CURRENT_PROJECT
                    static::createWhere(
                        (new JoinField())
                            ->setBase(PL::getBase())
                            ->setTable(PL::$_table)
                            ->setField(PL::PROJECT_ID),
                        CURRENT_PROJECT
                    )
                ]
            )
        )
        ->order([
            (new FieldHandler())
                ->setBase(PL::getBase())
                ->setTable(PL::$_table)
                ->setField(PL::PROJECT_LANGUAGE_ORDER)
                ->setIfNull('9999')
        ])
        ->result();
}
```

What Happens Here
- A JoinField is created for the LANGUAGE_ID field of the main table.

- The project_languages table is added to the JOIN.

- Two conditions are added:

- A join on the language_id field, a filter for project_id = CURRENT_PROJECT

For sorting, FieldHandler is used with setIfNull(), which replaces NULL with 9999 so that records without a sort order appear at the end.

####Example: Sorting with NULL Replacement
```php
$order = [
    (new FieldHandler())
        ->setBase('users')
        ->setTable('profiles')
        ->setField('display_order')
        ->setIfNull('9999')
];
```
This adds to ORDER BY: IFNULL(users.profiles.display_order, "9999")

####Example: Aggregate Functions
```php
$field = (new FieldHandler())
    ->setBase('orders')
    ->setTable('order_items')
    ->setField('price')
    ->setMax()
    ->setAlias('max_price');
```
Result in SELECT: MAX(orders.order_items.price) AS max_price

####Example: Field Concatenation (CONCAT)
```php
$field = (new FieldHandler())
    ->setConcat(['first_name', 'last_name'])
    ->setAlias('full_name');
```
Result in SELECT: CONCAT(first_name, last_name) AS full_name

####Example: GROUP_CONCAT
```php
$field = (new FieldHandler())
    ->setBase('products')
    ->setTable('categories')
    ->setField('name')
    ->setGroupConcat()
    ->setAlias('categories_list');
```
Result in SELECT: GROUP_CONCAT(products.categories.name) AS categories_list

####Example: Value Replacement (REPLACE)
```php
$field = (new FieldHandler())
    ->setBase('content')
    ->setTable('pages')
    ->setField('url')
    ->setReplace('old-domain.com', 'new-domain.com');
```
Result in SELECT: REPLACE(content.pages.url, 'old-domain.com', 'new-domain.com')

##Usage Notes
JoinField and FieldHandler are designed to work with Select and createWhere.

JoinField can be used both as a field and as a value in createWhere.

FieldHandler supports method chaining, making the code more readable.

When working with multiple databases, always specify setBase() for each field.

## What's next?

- ➡️ [Section 19. Models](/docs/19-models)
- ➡️ [Section 20. JSON Data](/docs/20-orm-json)
- ➡️ [Section 21. Controller Types](/docs/21-controllers)