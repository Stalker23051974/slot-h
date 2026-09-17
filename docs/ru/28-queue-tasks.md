<small>Предыдущие статьи:

- ➡️ [Раздел 25. Защита доступа](/docs/25-security-access)
- ➡️ [Раздел 26. Принцип локализации](/docs/26-localization-principle)
- ➡️ [Раздел 27. Запуск сервера очередей](/docs/27-queue-start)
</small>
# SLOT-H: Задачи сервера очередей

В системе можно создавать собственные задачи для выполнения в фоновом режиме. Для этого необходимо добавить новый тип задачи и реализовать его обработку.

---

## Что такое задача

Задача - это единица работы, которая выполняется воркером асинхронно. Каждая задача имеет свой тип (action), параметры и статус. Задачи хранятся в таблице `queues`.

---

## Создание нового типа задачи

Для добавления новой задачи необходимо выполнить несколько шагов.

### 1. Добавить константу типа задачи

В классе `Queue` (`Modules/Base/Helpers/Queues/Queue.php`) добавить константу для нового типа:

```php
const QUEUE_ACTION_NEW_TASK = 10;
```
### 2. Добавить обработчик в CLI-контроллер
В классе Cli (Modules/Base/Controllers/Cli.php) добавить метод для обработки задачи:

```php
public function newTaskAction()
{
    $id = $this->_request->getParams();
    $this->setPid($id);
    
    $queue = new Q();
    $require = $queue->getRequire($id);
    
    // Логика обработки задачи
    // ...
    
    $queue->success($id);
}
```
Метод должен:

Получить идентификатор задачи через $this->_request->getParams()

Установить PID через $this->setPid($id)

Получить параметры задачи через $queue->getRequire($id)

Выполнить необходимую логику

Отметить задачу как выполненную через $queue->success($id)

Или как ошибочную через $queue->error($id)

### 3. Добавить обработчик в метод getNew
В классе QueueSql (Modules/Application/Helpers/Queues/QueueSql.php) в методе getNew() добавить обработку нового типа:

```php
case Queue::QUEUE_ACTION_NEW_TASK:
    $this->counter($result, Queue::NEW_TASK_LOG);
    pclose(popen(C::get('console_php').' ' . ROOT_PATH . Queue::NEW_TASK_ACTION . ' -stage=' . CONF_NAME . ' -request=' . $v->setUse()->_id().' &', 'r'));
    break;
```
### 4. Добавить команду выполнения
В классе Queue добавить константу с CLI-командой:

```php
const NEW_TASK_ACTION = 'Application'.DIRECTORY_SEPARATOR.'Tools'.DIRECTORY_SEPARATOR.'cli.php -module=Base -controller=Cli -action=newTask';
```
### 5. Добавить задачу в очередь
Для добавления задачи в очередь используется метод push:

```php
$queue = new Q();
$queue->push(Q::QUEUE_ACTION_NEW_TASK, $params, $time);
```
Где:

QUEUE_ACTION_NEW_TASK - тип задачи

$params - параметры задачи (массив)

$time - время выполнения (timestamp)

#### Пример готовой задачи
Ниже пример задачи для отправки email. В демо-версии она используется как образец.

Метод в CliController:

```php
public function mailAction()
{
    $id = $this->_request->getParams();
    $this->setPid($id);
    
    $queue = new Q();
    $require = $queue->getRequire($id);
    
    // Подготовка и отправка письма
    $mail = new Mail();
    $result = $mail->send($require['template'], $require['params']);
    
    if ($result) {
        $queue->success($id);
    } else {
        $queue->error($id);
    }
}
```
Добавление в очередь:

```php
$queue->push(Q::QUEUE_ACTION_MAIL, [
    'template' => $templateId,
    'params' => $mailParams,
    'email' => $userEmail
], time());
```
## Возобновляемые задачи
Для задач, которые должны выполняться периодически, используется механизм возобновления. После завершения задачи (успешно или с ошибкой) она автоматически добавляется в очередь снова через заданный интервал.

Для этого в методе обработки задачи после её завершения вызывается:

```php
$this->getStack()[Q::QUEUE_ACTION_xxx]([]);
```
Где getStack() возвращает массив с настройками периодичности для каждого типа задачи.

## Логирование
Для отладки задач используется метод dflog:

```php
\Application\Helpers\DfDebug::dflog($message, $title, $file, $debug, $separate);
```
Рекомендуется создавать отдельные файлы логов для разных типов задач ($separate = true).

## Особенности реализации
Каждая задача выполняется в отдельном процессе, что обеспечивает изоляцию и безопасность. При ошибке в задаче она не влияет на другие задачи.

Параметры задачи передаются в формате JSON и могут содержать любые данные, необходимые для обработки.

---

## Что дальше?

- ➡️ [Раздел 29. Хелперы](/docs/29-utils-helpers)
- ➡️ [Раздел 30. Демо-версия vs Полная версия](/docs/30-camparison)
- ➡️ [Раздел 31. FAQ](/docs/31-faq)
