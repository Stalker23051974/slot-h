<?php

/**
 * SQL-based queue server implementation.
 *
 * This class implements the queue interface using a SQL database
 * as the backend storage for tasks. It handles:
 * - Task retrieval (getNew, getRequire)
 * - Task creation (push)
 * - Task status management (success, error, remove)
 * - Background process execution via popen
 *
 * Tasks are processed by spawning background CLI processes
 * using popen(). Each task type has a corresponding CLI command
 * that handles the actual processing.
 *
 * @package   Application\Helpers\Queues
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Helpers\Queues;

use \Modules\Base\Models\DbTables\Queue as Q;
use \Config\CC as C;
use \Application\Helpers\DfDebug as dg;

class QueueSql implements InterfaceQueue
{
    /**
     * Retrieve and process new tasks from the queue.
     *
     * Fetches pending tasks from the database and spawns background
     * processes for each task based on its type.
     *
     * Supported task types:
     * - QUEUE_ACTION_MAIL: Email sending via cli.php
     * - QUEUE_ACTION_IMAGE: Image processing via cli.php
     * - QUEUE_ACTION_DUMP: Database dump via cli.php
     *
     * Each task is spawned as a background process using popen()
     * to avoid blocking the queue worker.
     *
     * @return array|bool Task processing statistics (counts by type)
     */
    public function getNew()
    {
        $result = false;

        if ($res = Q::getNew()) {
            $result = [];
            /** @var \Modules\Base\Models\Queue[] $res */
            foreach ($res as $v) {
                switch ($v->getQueueAction()) {
                    case Queue::QUEUE_ACTION_DUMP:
                        dg::dflog('start process DUMP', 'worker', \Modules\Base\Controllers\Cli::QUEUE_LOG_FILE, false, true);
                        $this->counter($result, Queue::DUMP_LOG);
                        pclose(popen(
                            C::get('console_php') . ' ' . ROOT_PATH . Queue::DUMP_ACTION
                            . ' -stage=' . CONF_NAME . ' -request=' . $v->setUse()->_id() . ' &',
                            'r'
                        ));
                        break;

                    default:
                        dg::dflog('process ' . $v->getQueueAction() . ' not exists', 'worker', \Modules\Base\Controllers\Cli::QUEUE_LOG_FILE, false, true);
                        $this->counter($result, Queue::UNDEFINED);
                        break;
                }
            }
        } else {
            dg::dflog('new process not found', 'worker', \Modules\Base\Controllers\Cli::QUEUE_LOG_FILE, false, true);
        }

        return $result;
    }

    /**
     * Increment the counter for a task type.
     *
     * @param array  $result  Reference to result array
     * @param string $element Task type name
     */
    private function counter(&$result, $element)
    {
        if (!isset($result[$element])) {
            $result[$element] = 0;
        }
        $result[$element]++;
    }

    /**
     * Retrieve a specific task by ID and decode its parameters.
     *
     * @param int|string $id Task identifier
     *
     * @return array Decoded task parameters
     */
    public function getRequire($id)
    {
        $res = Q::getRow($id);
        /** @var \Modules\Base\Models\Queue $res */
        return dfJsonDecode($res->getQueueParams(), true);
    }

    /**
     * Add a new task to the queue.
     *
     * Creates a new queue entry with the specified action,
     * parameters, and scheduled execution time.
     *
     * @param int|string $action Task type (e.g., QUEUE_ACTION_MAIL)
     * @param array      $params Task parameters (JSON-encoded)
     * @param int        $time   Scheduled execution timestamp
     *
     * @return array|bool|mixed New task ID or false on failure
     */
    public function push($action, $params, $time)
    {
        return (new \Modules\Base\Models\Queue())
            ->setQueueAction($action)
            ->setQueueParams(dfJsonEncode($params))
            ->setQueueTime($time)
            ->setQueueStatus(Q::QUEUE_STATUS_NEW)
            ->setQueueFirstStart('0')
            ->save();
    }

    /**
     * Remove a task from the queue.
     *
     * @param int|string $id Task identifier
     *
     * @return array|bool|mixed Deletion result
     */
    public function remove($id)
    {
        return Q::remove([Q::$_index => $id]);
    }

    /**
     * Mark a task as failed/error.
     *
     * @param int|string $id Task identifier
     *
     * @return array|bool|mixed Update result
     */
    public function error($id)
    {
        $res = Q::getRow($id);
        /** @var \Modules\Base\Models\Queue $res */
        return $res->setQueueStatus(Q::QUEUE_STATUS_ERROR)->save();
    }

    /**
     * Mark a task as successfully completed.
     *
     * @param int|string $id Task identifier
     *
     * @return array|bool|mixed Update result
     */
    public function success($id)
    {
        $res = Q::getRow($id);
        /** @var \Modules\Base\Models\Queue $res */
        return $res->setQueueStatus(Q::QUEUE_STATUS_SUCCESS)->save();
    }
}