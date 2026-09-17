<?php

/**
 * Queue interface for task/job management.
 *
 * This interface defines the contract for queue engine implementations
 * that manage asynchronous background tasks. The queue system handles:
 * - Task creation (push)
 * - Task retrieval (getNew, getRequire)
 * - Task status management (success, error, remove)
 *
 * Tasks are processed by the queue server daemon running via CLI.
 * Supported task types include email sending, image processing,
 * data parsing, localization, and more.
 *
 * @package   Application\Helpers\Queues
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Helpers\Queues;

interface InterfaceQueue
{
    /**
     * Retrieve a new task from the queue.
     *
     * Fetches the next pending task that is ready for processing.
     * Used by the queue worker to get the next job to execute.
     *
     * @return mixed Queue task data or false if none available
     */
    public function getNew();

    /**
     * Retrieve a specific task by ID.
     *
     * Used for task debugging or manual processing
     * via the CLI interface.
     *
     * @param mixed $id Task identifier
     *
     * @return mixed Queue task data
     */
    public function getRequire($id);

    /**
     * Add a new task to the queue.
     *
     * Creates a new queue entry with the specified action,
     * parameters, and scheduled execution time.
     *
     * @param int|string $action Task type/action identifier
     * @param array      $params Task parameters (JSON-encoded)
     * @param int        $time   Scheduled execution timestamp
     *
     * @return mixed New task ID or success status
     */
    public function push($action, $params, $time);

    /**
     * Remove a task from the queue.
     *
     * Deletes a task completely from the queue.
     * Used when a task should be cancelled or discarded.
     *
     * @param mixed $id Task identifier
     *
     * @return mixed Deletion result
     */
    public function remove($id);

    /**
     * Mark a task as failed/error.
     *
     * Updates the task status to indicate that processing failed.
     * May trigger retry logic or logging for investigation.
     *
     * @param mixed $id Task identifier
     *
     * @return mixed Update result
     */
    public function error($id);

    /**
     * Mark a task as successfully completed.
     *
     * Updates the task status to indicate successful processing.
     * The task may be archived or removed from the active queue.
     *
     * @param mixed $id Task identifier
     *
     * @return mixed Update result
     */
    public function success($id);
}