<small>Предыдущие статьи:

- ➡️ [Раздел 17. Методы работы](/docs/17-orm-methods)
- ➡️ [Раздел 18. Использование JOIN](/docs/18-join)
- ➡️ [Раздел 19. Модели](/docs/19-models)
</small>
# SLOT-H: JSON-данные

В системе предусмотрен механизм хранения данных в формате JSON для полей, которые:
- могут отсутствовать у разных записей
- не используются в поиске
- не требуют индексации
- нуждаются в гибкой структуре, которая может меняться без изменения схемы БД

---

## Назначение

JSON-поля позволяют хранить произвольные наборы данных без необходимости создавать отдельные колонки в таблице. Это удобно для:

- дополнительных настроек пользователей
- метаданных, которые могут быть не у всех записей
- данных, структура которых может меняться со временем
- полей, которые не участвуют в WHERE-условиях

---

## Реализация

Для организации работы с JSON-данными в классе DbTable должны быть определены:

- поле типа `TEXT` для хранения JSON-строки
- константы для ключей JSON-объекта
- в соответствующей модели - свойство `$_dataField` с именем поля для данных (camelCase без префикса)

**Пример из модуля Base:**

DbTable `PersonProtected`:

```php
class PersonProtected extends \Application\Assistance\DatabaseNormal
{
    const PERSON_PROTECTED_ID = 'person_protected_id';
    const PERSON_ID = 'person_id';
    const PERSON_PROTECTED_DATA = 'person_protected_data';

    // Ключи JSON-объекта
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
Модель PersonProtected:

```php
class PersonProtected extends \Application\Assistance\Model
{
    public $person_protected_id;
    public $person_id;
    public $person_protected_data;

    protected $_dataField = 'PersonProtectedData';
}
```
Работа с JSON-данными в модели
После настройки становятся доступны методы для работы с JSON-данными:

##Получение значения
```php
$value = $model->getData(db\PersonProtected::VOICE);
```
Если ключ отсутствует, возвращается false.

##Установка значения
```php
$model->setProtected(db\PersonProtected::VOICE, 'voice_value');
```
##Установка нескольких значений
```php
$model->setProtected(false, false, [
    db\PersonProtected::VOICE => 'voice_value',
    db\PersonProtected::SKIN => 'dark'
]);
```
###Пример: добавление произвольных полей пользователю
Предположим, нужно добавить пользователю поле phone и telegram без изменения структуры базы данных.

####Шаг 1: Добавить константы в DbTables/PersonProtected:

```php
const PHONE = 'phone';
const TELEGRAM = 'telegram';
```
####Шаг 2: В модели использовать штатные методы:

```php
// Установка
$protected->setProtected(db\PersonProtected::PHONE, '+7 999 123-45-67');
$protected->setProtected(db\PersonProtected::TELEGRAM, '@username');

// Получение
$phone = $protected->getData(db\PersonProtected::PHONE);
$telegram = $protected->getData(db\PersonProtected::TELEGRAM);
```
Всё это работает без изменения базы данных, без миграций и без перезагрузки сервера.

##Ограничения
JSON-поля не предназначены для:

- полнотекстового поиска

- сортировки по значениям внутри JSON

- индексации вложенных полей

- условий WHERE по значениям внутри JSON

Для этих целей следует использовать обычные поля в таблице с соответствующими индексами.

---

## Что дальше?

- ➡️ [Раздел 21. Типы контроллеров](/docs/21-controllers)
- ➡️ [Раздел 22. Структура фронтенд части](/docs/22-layouts)
- ➡️ [Раздел 23. Выбор js-оболочки](/docs/23-js)
