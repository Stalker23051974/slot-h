<?php

namespace Modules\Base\Models;

use \Modules\Base\Models\DbTables as db;

/**
 * Queue model class.
 *
 * Represents a queue task record. Manages asynchronous background
 * task processing and tracking.
 *
 * @package   Modules\Base\Models
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method int getQueueId()
 * @method $this setQueueId(int $queue_id)
 * @method int getQueueStatus()
 * @method $this setQueueStatus(int $queue_status)
 * @method string getQueueParams()
 * @method $this setQueueParams(string $queue_params)
 * @method int getQueueAction()
 * @method $this setQueueAction(int $queue_action)
 * @method int getQueueTime()
 * @method $this setQueueTime(int $queue_time)
 * @method int getQueueFirstStart()
 * @method $this setQueueFirstStart(int $queue_first_start)
 * @method int getQueueProcessId()
 * @method $this setQueueProcessId(int $queue_process_id)
 */
class Queue extends \Application\Assistance\Model
{
    /** @var int Primary key */
    public $queue_id;

    /** @var int Task status */
    public $queue_status;

    /** @var string Task parameters (JSON) */
    public $queue_params;

    /** @var int Task action/type */
    public $queue_action;

    /** @var int Scheduled execution time */
    public $queue_time;

    /** @var int First start timestamp */
    public $queue_first_start;

    /** @var int Process ID */
    public $queue_process_id;

    /** JSON data field name */
    protected $_dataField = 'QueueParams';

    /**
     * Mark the task as "in use" (being processed).
     *
     * Sets first start time if not set, updates status to PROGRESS,
     * and saves the record.
     *
     * @return $this
     */
    public function setUse()
    {
        if ($this->getQueueFirstStart() < 1) {
            $this->setQueueFirstStart(CURRENT_TIME);
        }
        $this->setQueueStatus(db\Queue::QUEUE_STATUS_PROGRESS)->save();
        return $this;
    }
}