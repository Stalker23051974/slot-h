<small>Предыдущие статьи:

- ➡️ [Раздел 18. Использование JOIN](/docs/18-join)
- ➡️ [Раздел 19. Модели](/docs/19-models)
- ➡️ [Раздел 20. JSON-данные](/docs/20-orm-json)
</small>
# SLOT-H: Типы контроллеров

В системе существуют несколько типов контроллеров, каждый из которых предназначен для решения своих задач. Все контроллеры наследуются от базовых классов, расположенных в ядре.

---

## Базовый контроллер

Основной контроллер для работы с веб-интерфейсом. Наследуется от `Application\Assistance\Controller\Controller`. Используется для страниц, которые рендерят HTML.

Базовый контроллер выполняет роль ментора: он следит за доступом, проверяет права, управляет данными, но не вмешивается в работу экшена. Его задача - подготовить окружение и запустить экшен, а после его завершения передать результат в представление.

**В базовом контроллере доступны:**

- Объект запроса для получения параметров
- Объект представления для передачи данных в шаблон
- Методы редиректа
- Логирование действий пользователя
- Проверка прав доступа

**Жизненный цикл базового контроллера:**

1. Проверка прав доступа
2. Запуск экшена
3. Экшен возвращает данные
4. Контроллер передаёт данные в представление
5. Контроллер завершает работу

**Пример контроллера:**

```php
namespace Modules\Blog\Controllers;

class Index extends \Application\Assistance\Controller\Controller
{
    public function __construct($request)
    {
        parent::__construct($request);
    }

    public function indexAction()
    {
        $this->_view->append(['data' => 'Hello World']);
    }
}
```
##API-контроллер
Предназначен для создания JSON-API. Наследуется от Application\Assistance\Controller\ApiController.

Особенности:

Возвращает данные в формате JSON

Не использует шаблонизацию

Работает через объект представления ApiView

Поддерживает стандартный формат ответа

Пример:

```php
namespace Modules\Blog\Controllers;

class Api extends \Application\Assistance\Controller\ApiController
{
    public function __construct($request)
    {
        parent::__construct($request);
    }

    public function getAction()
    {
        $this->_view->_result = $this->setOk(['data' => 'value']);
    }
}
```
Ответ будет автоматически преобразован в JSON с добавлением служебных полей checksum и sign.

##Restful-контроллер
Предназначен для создания REST API. Наследуется от Application\Assistance\Controller\RestfulController.

Особенности:

Поддерживает HTTP-методы GET, POST, PUT, DELETE

Работает с кодами ответа HTTP

Использует RestfulView для представления

Пример:

```php
namespace Modules\Blog\Controllers;

class Rest extends \Application\Assistance\Controller\RestfulController
{
    public function __construct($request)
    {
        parent::__construct($request);
    }

    public function getAction()
    {
        // обработка GET-запроса
    }

    public function postAction()
    {
        // обработка POST-запроса
    }
}
```
CLI-контроллер
Предназначен для работы из командной строки. Наследуется от Application\Assistance\Controller\CliController.

Особенности:

Не использует сессии и куки

Не рендерит представления

Выполняется через CLI-воркер или cron

Используется для фоновых задач

Пример:

```php
namespace Modules\Base\Controllers;

class Cli extends \Application\Assistance\Controller\CliController
{
    public function __construct($request)
    {
        parent::__construct($request);
    }

    public function queueAction()
    {
        // обработка очереди задач
    }
}
```
Вызов из командной строки:

```bash
php Application/Tools/cli.php -stage=project -module=Base -controller=Cli -action=queue
```
##Особенности реализации
Контроллер не знает, с какой моделью он работает. Программист явно вызывает нужную модель в коде экшена.

Все контроллеры используют трейт TraitClass для доступа к общим методам.

Вызываемые экшены в контроллерах должны иметь суффикс Action. Например, indexAction, editAction. Приватные методы именуются так, как удобно разработчику - главное, чтобы было понятно человеку.

Для проверки прав доступа используется массив _access в контроллере. Права проверяются в Preloader модуля перед вызовом экшена.


##Создание нового контроллера

Есть два варианта:
- создать вручную (достаточно копипаста существующего и убрать из него не нужное);
- выполнить консольную команду и по подсказке скрипта вызвать его с требуемыми данными, в результате чего получите контроллер, представления для всех экшенов, а таже css и js файлы. 
```bash
php Application/Tools/createController.php
```

---

## Что дальше?

- ➡️ [Раздел 22. Структура фронтенд части](/docs/22-layouts)
- ➡️ [Раздел 23. Выбор js-оболочки](/docs/23-js)
- ➡️ [Раздел 24. Динамическая подпись](/docs/24-security-sign)
