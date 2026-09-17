<small>Previous articles:

- ➡️ [Section 24. Dynamic Signature](/docs/24-security-sign)
- ➡️ [Section 25. Access Protection](/docs/25-security-access)
- ➡️ [Section 26. Localization Principles](/docs/26-localization-principle)
</small>

# SLOT-H: Queue Server Startup

The system implements its own queue server for executing background tasks. It runs via a CLI worker launched on a schedule and processes tasks asynchronously.

---

## What is a Queue Server

The queue server is a mechanism for executing tasks that should not run synchronously within an HTTP request. Examples include: sending emails, creating database dumps, parsing data, and automatic text translation.

Tasks are placed in a queue and processed by a background process. This frees the user interface from waiting and allows resource-intensive operations to run without affecting site speed.

---

## Queue Server Structure

The queue server consists of two main components:

- **Daemon** (`demonAction`) - a supervisor process launched via cron every minute. It checks if the worker is running and restarts it if necessary. The daemon also monitors task execution and cleans up outdated logs.

- **Worker** (`queueAction`) - the main process that processes tasks from the queue. It runs in an infinite loop: takes a new task, starts processing it, waits a second, and repeats.

---

## Daemon Startup

The daemon is launched via cron. The following entry is added to crontab:

```bash
*/1 * * * * php <project_path>/Application/Tools/cli.php -stage=<ini_file_name> -module=Base -controller=Cli -action=demon
```
The daemon checks for a running worker every minute. If the worker is not found, the daemon starts it and terminates.

## Worker Startup
The worker is started automatically by the daemon. The launch command is:

```bash
php Application/Tools/cli.php -stage=<ini_file_name> -module=Base -controller=Cli -action=queue -request=0
```
The worker runs in an infinite loop until the specified lifetime (QUEUE_LIFETIME) expires. After the loop ends, the worker is restarted by the daemon.

## Task Processing
The worker retrieves tasks from the queues table. For each task, its type (action) is determined and the corresponding handler is launched.

Task types in the demo version:

**dump** - creates a database dump

Each task runs in a separate process, allowing multiple tasks to be processed in parallel. The number of concurrent processes is regulated by a parameter to avoid excessive server load.

## Task Types
Tasks in the queue are divided into two types:

Renewable - tasks that must run periodically. For example, currency or exchange rate parsers. After such a task completes (successfully or with an error), it is automatically added to the queue again after a specified interval.

One-time - tasks that run once. For example, sending an email or push notifications. After completion, such tasks are not recreated.

For renewable tasks, it is enough to start the process once, setting the frequency. For example, a currency fetching task with a daily frequency: after completion, it will be automatically added to the queue after 86400 seconds.

## Process Monitoring
The daemon regularly checks the status of running processes. If a process terminates abnormally (due to an error), it is marked by the worker as aborted (status QUEUE_STATUS_ABORT). This allows tracking issues and taking action.

## Logging
The queue server writes logs to the Config/Logs/<project_name> folder. The main log files are:

- **queue** - daemon and worker logs
- **dump** - dump creation logs

Logs help monitor server operation and diagnose problems.

##Server Shutdown
To shut down the server:

Remove the task from cron

Abort all processes using cli.php

Command to stop all queue processes:

```bash
pkill -f "cli.php.*-action=queue"
```
Or a softer approach - find and terminate specific processes via:

```bash
ps aux | grep cli.php
```
##Features
The queue server runs only in CLI mode. Launching via the web interface is impossible.

The daemon and worker use the same project configuration file.

Tasks can be scheduled for a specific time via the queue_time field. If no time is specified, the task runs immediately.

On task execution error, its status changes to QUEUE_STATUS_ERROR, and it can be reprocessed.

Abnormally terminated processes are marked with the QUEUE_STATUS_ABORT status.

##What's next?
- ➡️ [Section 28. Queue Server Tasks](/docs/28-queue-tasks)
- ➡️ [Section 29. Helpers](/docs/29-utils-helpers)
- ➡️ [Section 30. Demo vs Full Version](/docs/30-camparison)
