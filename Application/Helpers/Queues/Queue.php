<?php

/**
 * Queue management facade for background task processing.
 *
 * This class provides a unified interface to the queue system,
 * delegating operations to the configured queue server implementation
 * (SQL, Redis, etc.). It handles task creation, retrieval, and status
 * management for asynchronous background jobs.
 *
 * Supported task types:
 * - MAIL: Email sending (immediate execution)
 * - IMAGE: Image processing (compression, normalization, resize)
 * - DUMP: Database dump creation (scheduled backups)
 *
 * The queue server runs as a CLI daemon via cron and processes tasks
 * according to their scheduled time. Each task type has a corresponding
 * CLI action in the Base/Cli controller.
 *
 * Usage example:
 *   $queue = new Queue();
 *   $queue->push(Queue::QUEUE_ACTION_MAIL, $mailData, time() + 60);
 *
 * @package   Application\Helpers\Queues
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Helpers\Queues;

use \Application\Helpers\DfDebug as dg;
use \Config\CC as C;

class Queue implements InterfaceQueue
{
    // ============================================================================
    // TASK EXECUTION COMMANDS
    // ============================================================================

    /** CLI command for mail queue processing */
    const MAIL_ACTION = 'Application' . DIRECTORY_SEPARATOR . 'Tools' . DIRECTORY_SEPARATOR . 'cli.php -module=Base -controller=Cli -action=mail';

    /** CLI command for image processing queue */
    const IMAGE_ACTION = 'Application' . DIRECTORY_SEPARATOR . 'Tools' . DIRECTORY_SEPARATOR . 'cli.php -module=Base -controller=Cli -action=image';

    /** CLI command for database dump queue */
    const DUMP_ACTION = 'Application' . DIRECTORY_SEPARATOR . 'Tools' . DIRECTORY_SEPARATOR . 'cli.php -module=Base -controller=Cli -action=dump';

    // ============================================================================
    // TASK TYPE CONSTANTS
    // ============================================================================

    /** Mail sending task type */
    const QUEUE_ACTION_MAIL = 1;

    /** Image processing task type */
    const QUEUE_ACTION_IMAGE = 2;

    /** Database dump task type */
    const QUEUE_ACTION_DUMP = 3;

    // ============================================================================
    // TASK CONFIGURATION
    // ============================================================================

    /** List of long-running actions (reserved for future use) */
    const LONG_ACTIONS = [];

    // ============================================================================
    // MAIL TASK PARAMETER CONSTANTS
    // ============================================================================

    /** Mail template identifier */
    const MAIL_TEMPLATE = 'template';

    /** Recipient person ID */
    const MAIL_PERSON_ID = 'person_id';

    /** Mail parameters (JSON-encoded data for template) */
    const MAIL_PARAMS = 'params';

    /** Recipient email address */
    const MAIL_ADDRESS = 'email';

    // ============================================================================
    // MAIL ACTION TYPES
    // ============================================================================

    /** Send mail by hash (for verification/activation) */
    const ACTION_SEND_MAIL_HASH = 0;

    /** Send mail by remind (for password reset) */
    const ACTION_SEND_MAIL_REMIND = 1;

    // ============================================================================
    // IMPORT TASK PARAMETER CONSTANTS
    // ============================================================================

    /** Import target file path */
    const IMPORT_TARGET_FILE = 'file';

    /** Import target table name */
    const IMPORT_TARGET_TABLE = 'table';

    /** Import target type (CSV, XLS, etc.) */
    const IMPORT_TARGET_TYPE = 'type';

    // ============================================================================
    // LOGGING CONSTANTS
    // ============================================================================

    /** Undefined/unknown task type */
    const UNDEFINED = 'Undefined';

    /** Mail task log category */
    const MAIL_LOG = 'Mail';

    /** Image task log category */
    const IMAGE_LOG = 'Image';

    /** Dump task log category */
    const DUMP_LOG = 'Dump';

    /** @var \Application\Helpers\Queues\InterfaceQueue Queue server implementation */
    protected $_server;

    /**
     * Initialize the queue facade.
     *
     * Loads the configured queue server implementation
     * (e.g., QueueSql, QueueRedis) based on the 'queue_server'
     * configuration value.
     */
    public function __construct()
    {
        $class = '\Application\Helpers\Queues\Queue' . C::get('queue_server');
        $this->_server = new $class();
    }

    /**
     * Retrieve and process new tasks from the queue.
     *
     * Fetches pending tasks and logs the activity.
     * Used by the queue daemon to get the next batch of jobs.
     *
     * @return mixed|void
     */
    public function getNew()
    {
        if ($result = $this->_server->getNew()) {
            $res = [];
            foreach ($result as $k => $v) {
                $res[] = $k . ': ' . $v . ' proc.';
            }
            dg::dflog(
                "\n\t\t" . implode("\n\t\t", $res),
                'server ' . C::get('queue_server'),
                \Modules\Base\Controllers\Cli::QUEUE_LOG_FILE,
                false,
                true
            );
        }
    }

    /**
     * Retrieve a specific task by ID.
     *
     * @param int|string $id Task identifier
     *
     * @return mixed Task data
     */
    public function getRequire($id)
    {
        return $this->_server->getRequire($id);
    }

    /**
     * Add a new task to the queue.
     *
     * @param int|string $action Task type (e.g., QUEUE_ACTION_MAIL)
     * @param array      $params Task parameters
     * @param int        $time   Scheduled execution timestamp (default: 1 second from now)
     *
     * @return mixed New task ID or success status
     */
    public function push($action, $params, $time = 1)
    {
        return $this->_server->push($action, $params, $time);
    }

    /**
     * Remove a task from the queue.
     *
     * @param int|string $id Task identifier
     *
     * @return mixed Deletion result
     */
    public function remove($id)
    {
        return $this->_server->remove($id);
    }

    /**
     * Mark a task as failed/error.
     *
     * @param int|string $id Task identifier
     *
     * @return mixed Update result
     */
    public function error($id)
    {
        return $this->_server->error($id);
    }

    /**
     * Mark a task as successfully completed.
     *
     * @param int|string $id Task identifier
     *
     * @return mixed Update result
     */
    public function success($id)
    {
        return $this->_server->success($id);
    }
}