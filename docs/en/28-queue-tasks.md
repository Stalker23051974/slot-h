<small>Previous articles:

- ➡️ [Section 25. Access Protection](/docs/25-security-access)
- ➡️ [Section 26. Localization Principles](/docs/26-localization-principle)
- ➡️ [Section 27. Queue Server Startup](/docs/27-queue-start)
</small>

# SLOT-H: Queue Server Tasks

The system allows creating custom tasks for background execution. To do this, you need to add a new task type and implement its processing.

---

## What is a Task

A task is a unit of work executed asynchronously by the worker. Each task has its own type (`action`), parameters, and status. Tasks are stored in the `queues` table.

---

## Creating a New Task Type

To add a new task, several steps are required.

### 1. Add a Task Type Constant

In the `Queue` class (`Modules/Base/Helpers/Queues/Queue.php`), add a constant for the new type:

```php
const QUEUE_ACTION_NEW_TASK = 10;
```
###2. Add a Handler to the CLI Controller
In the Cli class (Modules/Base/Controllers/Cli.php), add a method to process the task:

```php
public function newTaskAction()
{
    $id = $this->_request->getParams();
    $this->setPid($id);
    
    $queue = new Q();
    $require = $queue->getRequire($id);
    
    // Task processing logic
    // ...
    
    $queue->success($id);
}
```
The method must:

Retrieve the task ID via $this->_request->getParams()

Set the PID via $this->setPid($id)

Retrieve task parameters via $queue->getRequire($id)

Execute the necessary logic

Mark the task as completed via $queue->success($id)

Or as failed via $queue->error($id)

###3. Add a Handler to the getNew Method
In the QueueSql class (Modules/Application/Helpers/Queues/QueueSql.php), add handling for the new type in the getNew() method:

```php
case Queue::QUEUE_ACTION_NEW_TASK:
    $this->counter($result, Queue::NEW_TASK_LOG);
    pclose(popen(C::get('console_php').' ' . ROOT_PATH . Queue::NEW_TASK_ACTION . ' -stage=' . CONF_NAME . ' -request=' . $v->setUse()->_id().' &', 'r'));
    break;
```
###4. Add the Execution Command
In the Queue class, add a constant with the CLI command:

```php
const NEW_TASK_ACTION = 'Application'.DIRECTORY_SEPARATOR.'Tools'.DIRECTORY_SEPARATOR.'cli.php -module=Base -controller=Cli -action=newTask';
```
###5. Add a Task to the Queue
The push method is used to add a task to the queue:

```php
$queue = new Q();
$queue->push(Q::QUEUE_ACTION_NEW_TASK, $params, $time);
```
Where:

QUEUE_ACTION_NEW_TASK - task type

$params - task parameters (array)

$time - execution time (timestamp)

Example: Ready-made Task
Below is an example of an email sending task. In the demo version, it is used as a sample.

Method in CliController:

```php
public function mailAction()
{
    $id = $this->_request->getParams();
    $this->setPid($id);
    
    $queue = new Q();
    $require = $queue->getRequire($id);
    
    // Prepare and send email
    $mail = new Mail();
    $result = $mail->send($require['template'], $require['params']);
    
    if ($result) {
        $queue->success($id);
    } else {
        $queue->error($id);
    }
}
```
Adding to the queue:

```php
$queue->push(Q::QUEUE_ACTION_MAIL, [
    'template' => $templateId,
    'params' => $mailParams,
    'email' => $userEmail
], time());
```
##Renewable Tasks
For tasks that need to run periodically, a renewal mechanism is used. After a task completes (successfully or with an error), it is automatically re-added to the queue after a specified interval.

To do this, in the task processing method after completion, call:

```php
$this->getStack()[Q::QUEUE_ACTION_xxx]([]);
```
getStack() returns an array with periodicity settings for each task type.

##Logging
For task debugging, the dflog method is used:

```php
\Application\Helpers\DfDebug::dflog($message, $title, $file, $debug, $separate);
```
It is recommended to create separate log files for different task types ($separate = true).

##Implementation Features
Each task runs in a separate process, ensuring isolation and safety. An error in one task does not affect other tasks.

Task parameters are passed in JSON format and can contain any data needed for processing.

##What's next?
- ➡️ [Section 29. Helpers](/docs/29-utils-helpers)
- ➡️ [Section 30. Demo vs Full Version](/docs/30-camparison)
- ➡️ [Section 31. FAQ](/docs/31-faq)
