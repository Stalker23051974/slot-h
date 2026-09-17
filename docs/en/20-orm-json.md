<small>Previous articles:

- ➡️ [Section 17. Working Methods](/docs/17-orm-methods)
- ➡️ [Section 18. Using JOINs](/docs/18-join)
- ➡️ [Section 19. Models](/docs/19-models)
</small>

# SLOT-H: JSON Data

The system provides a mechanism for storing data in JSON format for fields that:
- may be absent in different records
- are not used in search queries
- do not require indexing
- need a flexible structure that can change without altering the database schema

---

## Purpose

JSON fields allow storing arbitrary sets of data without the need to create separate columns in the table. This is convenient for:

- additional user settings
- metadata that may not exist for all records
- data whose structure may change over time
- fields that are not used in WHERE conditions

---

## Implementation

To work with JSON data in the DbTable class, the following must be defined:

- a `TEXT` field for storing the JSON string
- constants for the JSON object keys
- in the corresponding model - the `$_dataField` property with the data field name (camelCase without prefix)

**Example from the Base module:**

DbTable `PersonProtected`:

```php
class PersonProtected extends \Application\Assistance\DatabaseNormal
{
    const PERSON_PROTECTED_ID = 'person_protected_id';
    const PERSON_ID = 'person_id';
    const PERSON_PROTECTED_DATA = 'person_protected_data';

    // JSON object keys
    const SKIN = 'skin';
    const TRANSLATE_MODE = 'translate_mode';
    const VOICE = 'voice';
    const REGISTRATION = 'registration';
    const LOCATIONS = 'locations';
    const SHOW_HELP = 'show_help';
    const DEVICE_TOKEN = 'device_token';
    const AUTH_CODE = 'auth_code';

    public static $_fields = [
        self::PERSON_PROTECTED_ID => [],
        self::PERSON_ID => [
            self::FP_INDEX => true,
            self::FP_LINK => Person::class
        ],
        self::PERSON_PROTECTED_DATA => [self::FP_TYPE => self::TYPE_TEXT],
    ];
}
```
Model `PersonProtected`:

```php
class PersonProtected extends \Application\Assistance\Model
{
    public $person_protected_id;
    public $person_id;
    public $person_protected_data;

    protected $_dataField = 'PersonProtectedData';
}
```
##Working with JSON Data in the Model
After configuration, methods for working with JSON data become available:

###Getting a value
```php
$value = $model->getData(db\PersonProtected::VOICE);
```
If the key does not exist, false is returned.

###Setting a value
```php
$model->setProtected(db\PersonProtected::VOICE, 'voice_value');
```
###Setting multiple values
```php
$model->setProtected(false, false, [
    db\PersonProtected::VOICE => 'voice_value',
    db\PersonProtected::SKIN => 'dark'
]);
````
##Example: Adding custom fields to a user
Suppose you need to add phone and telegram fields to a user without changing the database structure.

###Step 1: Add constants to DbTables/PersonProtected:
```php
const PHONE = 'phone';
const TELEGRAM = 'telegram';
```
###Step 2: Use standard methods in the model:
```php
// Setting
$protected->setProtected(db\PersonProtected::PHONE, '+7 999 123-45-67');
$protected->setProtected(db\PersonProtected::TELEGRAM, '@username');

// Getting
$phone = $protected->getData(db\PersonProtected::PHONE);
$telegram = $protected->getData(db\PersonProtected::TELEGRAM);
```
This all works without changing the database, without migrations, and without restarting the server.

##Limitations
JSON fields are not designed for:

- full-text search
- sorting by values inside JSON
- indexing nested fields
- WHERE conditions based on values inside JSON

For these purposes, regular table fields with appropriate indexes should be used.

##What's next?
- ➡️ [Section 21. Controller Types](/docs/21-controllers)
- ➡️ [Section 22. Frontend Structure](/docs/22-layouts)
- ➡️ [Section 23. JS Library Choice](/docs/23-js)
