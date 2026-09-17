<small>Previous articles:

- ➡️ [Section 16. ORM Principles](/docs/16-orm-principles)
- ➡️ [Section 17. Working Methods](/docs/17-orm-methods)
- ➡️ [Section 18. Using JOINs](/docs/18-join)
</small>

# SLOT-H: Models

A model in the system is a class that represents a single record in a database table. It inherits from the base class `Application\Assistance\Model` and contains only property declarations corresponding to table fields.

---

## Model Minimalism

Models contain no logic. They only declare properties and inherit from the base class. All getters, setters, and relationship methods are located in the base `Model` class.

**Example model:**

```php
<?php

namespace Modules\Blog\Models;

/**
 * Post model class.
 *
 * Represents a post record.
 *
 * @package   Modules\Blog\Models
 *
 * @method int getPostId()
 * @method $this setPostId(int $post_id)
 * @method string getPostText()
 * @method $this setPostText(string $post_text)
 * @method string getPostAuthor()
 * @method $this setPostAuthor(string $post_author)
 * @method int getPostTime()
 * @method $this setPostTime(int $post_time)
 */
class Post extends \Application\Assistance\Model
{
    /** @var int Primary key */
    public $post_id;

    /** @var string Post text */
    public $post_text;

    /** @var string Post author */
    public $post_author;

    /** @var int Post creation time */
    public $post_time;
}
```
##PHPDoc for Models
It is recommended to add PHPDoc blocks describing all getters and setters. This does not affect system functionality but makes the code more understandable for developers and allows IDEs to automatically suggest available methods.

```php
/**
 * @method int getPostId()
 * @method $this setPostId(int $post_id)
 * @method string getPostText()
 * @method $this setPostText(string $post_text)
 * @method string getPostAuthor()
 * @method $this setPostAuthor(string $post_author)
 * @method int getPostTime()
 * @method $this setPostTime(int $post_time)
 */
```
All getters and setters are automatically generated in the base Model class based on field names. For the post_text field, methods getPostText() and setPostText() are created.

## Relationship with DbTables
The model is linked to DbTables through naming conventions. For the Post model, the DbTable Post is used in the namespace Modules\Blog\Models\DbTables.

Example DbTable:

```php
<?php

namespace Modules\Blog\Models\DbTables;

class Post extends \Application\Assistance\DatabaseExtend
{
    const POST_ID = 'post_id';
    const POST_TEXT = 'post_text';
    const POST_AUTHOR = 'post_author';
    const POST_TIME = 'post_time';

    public static $_table = 'posts';
    public static $_index = self::POST_ID;

    public static $_fields = [
        self::POST_ID => [],
        self::POST_TEXT => [self::FP_TYPE => self::TYPE_TEXT],
        self::POST_AUTHOR => [self::FP_TYPE => self::TYPE_STRING],
        self::POST_TIME => [self::FP_TYPE => self::TYPE_INT],
    ];
}
````
The base Model class automatically determines which DbTable corresponds to the model and uses it for executing queries.

##Automatic Getters and Setters
The base Model class provides automatic getters and setters for all properties declared in the model.

```php
$post->setPostText('Post text');
$text = $post->getPostText();
```
Getters and setters are formed based on field names. For the post_text field, getPostText and setPostText methods are created.

##Working with Relationships
The model supports automatic relationships through link, lots, and related methods.

###linkField()
Retrieves a related object (one-to-one).

```php
$country = $person->linkCountryId();
```
The relationship is determined by the field with the _id suffix. FP_LINK must be specified in the DbTable for this field:

```php
self::COUNTRY_ID => [self::FP_LINK => \Modules\Geo\Models\DbTables\Country::class]
```
###lotsFieldInModuleByField()
Retrieves a collection of related objects (one-to-many).

```php
$posts = $person->lotsPostInBlogByPersonId();
```
The method is formed using the pattern: lots{Entity}In{Module}By{Field}.

###relatedFieldInModuleByField()
Retrieves the reverse relationship (belongs-to).

```php
$person = $post->relatedPersonInBaseByPostId();
```
The method is formed using the pattern: related{Entity}In{Module}By{Field}.

###Converting to Array
The model can be converted to an array using the toArray() method.

```php
$data = $post->toArray();
```
The method returns an associative array of all model fields with their types cast.

###Saving
The save() method is used to save the model.

```php
$post->setPostText('New text')->save();
```
The method returns the ID of the saved record.

##Features
Property names in the model must match field names in the table. The base Model class uses reflection to determine available fields and generate methods.

If a property is not declared in the model, the corresponding getter or setter will not work.

Models can contain additional methods for specific data logic, but they are not required in the demo version.

##What's next?
- ➡️ [Section 20. JSON Data](/docs/20-orm-json)
- ➡️ [Section 21. Controller Types](/docs/21-controllers)
- ➡️ [Section 22. Frontend Structure](/docs/22-layouts)
