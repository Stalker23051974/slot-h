<small>Предыдущие статьи:

- ➡️ [Раздел 14. Создание модуля](/docs/14-module-custom)
- ➡️ [Раздел 15. Структура базы данных](/docs/15-database-diagram)
- ➡️ [Раздел 16. Принципы ORM](/docs/16-orm-principles)
</small>
# SLOT-H: Методы работы

ORM предоставляет набор предопределённых методов для работы с данными, а также позволяет создавать собственные методы поиска в классах DbTables.

---

## Предопределённые методы

### getRow

Получение одной записи по первичному ключу или нескольких записей по массиву идентификаторов.

```php
$record = Table::getRow($id);
$records = Table::getRow([1, 2, 3]);
Метод возвращает модель если передан один идентификатор, или массив моделей если передан массив идентификаторов.
```
###getAll
Получение всех записей из таблицы.

```php
$all = Table::getAll();
```
Поддерживает пагинацию и сортировку:

```php
$page = 2; // вторая страница
$order = [Table::FIELD_NAME => true]; // true - ASC, false - DESC
$records = Table::getAll($page, $order);
```
###getCount
Получение количества записей.

```php
$count = Table::getCount();
```
Может принимать условия для подсчёта:

```php
$count = Table::getCount([Table::FIELD_STATUS => 1]);
```
###getByCondition
Получение записей по условиям, заданным через объект Select.

```php
$records = Table::getByCondition();
```
Метод используется внутри других методов, но может быть вызван напрямую для получения результата после построения запроса.

###remove
Удаление записей по условию.

```php
Table::remove([Table::FIELD_STATUS => 0]);
```
Для таблиц с soft delete (DatabaseExtend) метод устанавливает deleted_at вместо физического удаления.

###getByName
Получение записи по имени. Для работы метода в классе DbTable должно быть определено свойство $_name.

```php
$record = Table::getByName('John');
```
###save
Сохранение записи. Метод принимает массив полей и возвращает идентификатор сохранённой записи.

```php
$id = Table::save([Table::FIELD_NAME => 'John', Table::FIELD_STATUS => 1]);
```
Для таблиц с updated_at (DatabaseExtend) поле обновляется автоматически.

##Создание собственных методов поиска
Помимо предопределённых методов, в классах DbTables можно создавать собственные методы для специфических запросов. Для этого используется getSelect(), который возвращает объект Select для построения запроса.

###Пример из модуля Base:

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
Разбор примера:

*self::getSelect()* - создаёт новый объект Select для текущей таблицы.

*addWhere()* - добавляет условия в запрос. Принимает массив условий, созданных через createWhere.

*createWhere()* - создаёт объект условия для поля. Вместо строковых названий полей используются константы полей.

*pop()* - указывает, что нужно вернуть одну запись вместо массива.

*result()* - выполняет запрос и возвращает результат.

**Важно**: во всех методах вместо строковых названий полей используются константы, определённые в классе DbTable. Это обеспечивает единообразие и упрощает рефакторинг.

###Другие примеры
####Поиск по нескольким условиям с LIKE:

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
####Поиск с сортировкой:

```php
public static function getActiveSorted()
{
    return (self::getSelect())
        ->addWhere(self::createWhere(self::FIELD_STATUS, 1))
        ->order(self::FIELD_SORT)
        ->result();
}
```
####Поиск с пагинацией:

```php
public static function getByPage($page)
{
    return (self::getSelect())
        ->addWhere(self::createWhere(self::FIELD_STATUS, 1))
        ->page($page)
        ->result();
}
```
####Поиск с возвратом массива вместо модели:

```php
public static function getListAsArray()
{
    self::setModelResponse(false);
    return (self::getSelect())
        ->addWhere(self::createWhere(self::FIELD_STATUS, 1))
        ->result();
}
```
###Методы работы с объектом Select
Методы getRow, getAll, getByCondition используют объект Select для построения запросов. Select можно получить явно через метод getSelect.

```php
$select = Table::getSelect();
$select->addWhere(Table::createWhere(Table::FIELD_STATUS, 1));
$records = $select->result();
```
###Цепочка вызовов
Методы поддерживают цепочку вызовов для построения запросов.

```php
$records = Table::getSelect()
    ->addWhere(Table::createWhere(Table::FIELD_STATUS, 1))
    ->order(Table::FIELD_NAME)
    ->page(2)
    ->result();
```
###Кеширование
Методы поддерживают кеширование через параметр $cache.

```php
$record = Table::getRow($id, false, false, true, true);  // с кешем
$record = Table::getRow($id, false, false, true, false); // без кеша
```
###Режимы работы
Методы управления режимами работы позволяют временно изменять поведение ORM для текущего запроса. После выполнения запроса все флаги автоматически возвращаются в базовое состояние.

Это гарантирует, что изменение режима работы для одного запроса не повлияет на последующие операции.

####setModelResponse
Управляет форматом возвращаемых данных.

true - возвращаются модели

false - возвращаются массивы

```php
Table::setModelResponse(false);
$records = Table::getAll(); // вернёт массивы
```
После выполнения запроса режим возвращается к значению по умолчанию (true).

####setIgnoreExtend
Отключает фильтрацию по deleted_at для таблиц с soft delete. Позволяет получить удалённые записи.

```php
Table::setIgnoreExtend(true);
$records = Table::getAll(); // включает удалённые записи
```
После выполнения запроса флаг сбрасывается в false.

####setIgnoreProject
Отключает фильтрацию по project_id. Позволяет получить записи всех проектов.

```php
Table::setIgnoreProject(true);
$records = Table::getAll(); // включает записи всех проектов
```
После выполнения запроса флаг сбрасывается в false.

---

## Что дальше?

- ➡️ [Раздел 18. Использование JOIN](/docs/18-join)
- ➡️ [Раздел 19. Модели](/docs/19-models)
- ➡️ [Раздел 20. JSON-данные](/docs/20-orm-json)
