<small>Предыдущие статьи:

- ➡️ [Раздел 15. Структура базы данных](/docs/15-database-diagram)
- ➡️ [Раздел 16. Принципы ORM](/docs/16-orm-principles)
- ➡️ [Раздел 17. Методы работы](/docs/17-orm-methods)
</small>
---
# Сложные запросы

В демо-версии системы доступны два дополнительных инструмента для построения сложных запросов: класс `JoinField` для работы с JOIN-условиями и класс `FieldHandler` для операций над полями.

---

## JoinField

Класс `JoinField` предназначен для описания полей, используемых в JOIN-условиях. Он позволяет указать базу данных, таблицу и имя поля отдельно, что удобно при работе с несколькими базами данных или когда требуются алиасы таблиц.

Класс предоставляет fluent-интерфейс для установки свойств.

```php
$field = new JoinField()
    ->setBase('main_db')
    ->setTable('users')
    ->setField('user_id');
```

###Методы
| Метод	| Описание |
|-------|----------|
| setBase($base)	| Устанавливает имя базы данных |
| setTable($table)	| Устанавливает имя таблицы или алиас |
| setField($field)	| Устанавливает имя поля |

##FieldHandler
Класс FieldHandler позволяет применять различные операции к полям в SELECT-запросе. Поддерживаются агрегатные функции, условные операции и преобразования.

```php
$handler = new FieldHandler()
    ->setBase('main_db')
    ->setTable('users')
    ->setField('created_at')
    ->setIfNull('1970-01-01');
```
###Методы
| Метод	| Описание |
|-------|----------|
| setCount()	| Применяет COUNT к полю |
| setMin()	| Применяет MIN к полю |
| setMax()	| Применяет MAX к полю |
| setIfNull($override)	| Заменяет NULL на указанное значение |
| setReplace($search, $override)	| Заменяет вхождение $search на $override |
| setConcat($list)	| Объединяет несколько полей в строку |
| setGroupConcat()	| Применяет GROUP_CONCAT (агрегация строк) |
| setAlias($value)	| Устанавливает алиас для поля |
| setBase($value)	| Устанавливает базу данных |
| setTable($value)	| Устанавливает таблицу или алиас |
| setField($value)	| Устанавливает имя поля |
| setFlag($value)	| Устанавливает дополнительный флаг |

###Пример: JOIN с несколькими условиями

В этом примере JoinField используется для построения JOIN-условия между таблицами languages и project_languages с фильтрацией по project_id.

```php
public static function getActive()
{
    $pLanguage = PL::getBase() . '.' . PL::$_table;
    
    return (static::getSelect())
        ->addWhere(
            static::createWhere(
                // Основное поле
                (new JoinField())
                    ->setBase(self::getBase())
                    ->setTable(self::$_table)
                    ->setField(self::LANGUAGE_ID),
                null,
                Where::NOT_NULL,
                [PL::getBase(), PL::$_table],
                [
                    // Условие связи: language_id = language_id
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
                    // Условие фильтрации: project_id = CURRENT_PROJECT
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

####Что здесь происходит

- Создаётся JoinField для поля LANGUAGE_ID основной таблицы.

- В JOIN добавляется таблица project_languages.

- Добавляются два условия:

- Связь по полю language_id и фильтр по project_id = CURRENT_PROJECT

- Для сортировки используется FieldHandler с setIfNull(), который заменяет NULL на 9999, чтобы записи без порядка сортировки оказались в конце.

###Пример: сортировка с заменой NULL

```php
$order = [
    (new FieldHandler())
        ->setBase('users')
        ->setTable('profiles')
        ->setField('display_order')
        ->setIfNull('9999')
];
```
В результате в ORDER BY будет добавлено: IFNULL(users.profiles.display_order, "9999")

###Пример: агрегатные функции
```php
$field = (new FieldHandler())
    ->setBase('orders')
    ->setTable('order_items')
    ->setField('price')
    ->setMax()
    ->setAlias('max_price');
```
Результат в SELECT: MAX(orders.order_items.price) AS max_price

####Пример: объединение полей (CONCAT)
```php
$field = (new FieldHandler())
    ->setConcat(['first_name', 'last_name'])
    ->setAlias('full_name');
```

Результат в SELECT: CONCAT(first_name, last_name) AS full_name

####Пример: GROUP_CONCAT
```php
$field = (new FieldHandler())
    ->setBase('products')
    ->setTable('categories')
    ->setField('name')
    ->setGroupConcat()
    ->setAlias('categories_list');
```

Результат в SELECT: GROUP_CONCAT(products.categories.name) AS categories_list

####Пример: замена значения (REPLACE)
```php
$field = (new FieldHandler())
    ->setBase('content')
    ->setTable('pages')
    ->setField('url')
    ->setReplace('old-domain.com', 'new-domain.com');
```
Результат в SELECT: REPLACE(content.pages.url, 'old-domain.com', 'new-domain.com')

##Особенности использования
JoinField и FieldHandler предназначены для работы в связке с Select и createWhere.

JoinField может использоваться как в качестве поля, так и в качестве значения в createWhere.

FieldHandler поддерживает цепочку вызовов, что делает код более читаемым.

При использовании нескольких баз данных обязательно указывать setBase() для каждого поля.

## Что дальше?

- ➡️ [Раздел 19. Модели](/docs/19-models)
- ➡️ [Раздел 20. JSON-данные](/docs/20-orm-json)
- ➡️ [Раздел 21. Типы контроллеров](/docs/21-controllers)
