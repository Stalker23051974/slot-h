<small>Предыдущие статьи:

- ➡️ [Раздел 16. Принципы ORM](/docs/16-orm-principles)
- ➡️ [Раздел 17. Методы работы](/docs/17-orm-methods)
- ➡️ [Раздел 18. Использование JOIN](/docs/18-join)
</small>
# SLOT-H: Модели

Модель в системе - это класс, который представляет одну запись в таблице базы данных. Она наследуется от базового класса `Application\Assistance\Model` и содержит только объявление свойств, соответствующих полям таблицы.

---

## Минимализм моделей

Модель не содержит логики. Она только объявляет свойства и наследуется от базового класса. Все геттеры, сеттеры и методы работы со связями находятся в базовом классе `Model`.

**Пример модели:**

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
````
##PHPDoc для моделей
Рекомендуется добавлять PHPDoc-блоки с описанием всех геттеров и сеттеров. Это не влияет на работу системы, но делает код понятнее для разработчиков, а также позволяет IDE автоматически подсказывать доступные методы.

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
Все геттеры и сеттеры генерируются автоматически в базовом классе Model на основе названий полей. Для поля post_text создаются методы getPostText() и setPostText().

##Связь с DbTables
Модель связана с DbTables через соглашение об именовании. Для модели Post используется DbTable Post в пространстве имён Modules\Blog\Models\DbTables.

Пример DbTable:

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
Базовый класс Model автоматически определяет, какой DbTable соответствует модели, и использует его для выполнения запросов.

##Автоматические геттеры и сеттеры
Базовый класс Model предоставляет автоматические геттеры и сеттеры для всех полей, объявленных в модели.

```php
$post->setPostText('Текст поста');
$text = $post->getPostText();
```
Геттеры и сеттеры формируются на основе названий полей. Для поля post_text создаются методы getPostText и setPostText.

##Работа со связями
Модель поддерживает автоматические связи через методы link, lots и related.

###linkField()
Получение связанного объекта (one-to-one).

```php
$country = $person->linkCountryId();
```
Связь определяется по полю с суффиксом _id. В DbTable должно быть указано FP_LINK для этого поля:

```php
self::COUNTRY_ID => [self::FP_LINK => \Modules\Geo\Models\DbTables\Country::class]
```
###lotsFieldInModuleByField()
Получение коллекции связанных объектов (one-to-many).

```php
$posts = $person->lotsPostInBlogByPersonId();
```
Метод формируется по шаблону: lots{Entity}In{Module}By{Field}.

###relatedFieldInModuleByField()
Получение обратной связи (belongs-to).

```php
$person = $post->relatedPersonInBaseByPostId();
```
Метод формируется по шаблону: related{Entity}In{Module}By{Field}.

###Преобразование в массив
Модель может быть преобразована в массив через метод toArray().

```php
$data = $post->toArray();
```
Метод возвращает ассоциативный массив всех полей модели с приведёнными типами.

###Сохранение
Для сохранения модели используется метод save().

```php
$post->setPostText('Новый текст')->save();
```
Метод возвращает идентификатор сохранённой записи.

##Особенности
Имена свойств в модели должны совпадать с именами полей в таблице. Базовый класс Model использует reflection для определения доступных полей и генерации методов.

Если в модели не объявлено свойство, соответствующий геттер или сеттер не будет работать.

Модель может содержать дополнительные методы для специфической логики работы с данными, но в демо-версии они не требуются.

---

## Что дальше?

- ➡️ [Раздел 20. JSON-данные](/docs/20-orm-json)
- ➡️ [Раздел 21. Типы контроллеров](/docs/21-controllers)
- ➡️ [Раздел 22. Структура фронтенд части](/docs/22-layouts)
