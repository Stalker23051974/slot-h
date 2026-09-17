<?php

/**
 * CLI controller for background task processing.
 *
 * This controller handles all command-line operations for the system,
 * including queue worker daemon, health monitoring, database dumps,
 * email sending, and image processing. These methods are invoked
 * by cron or the queue server, not by HTTP requests.
 *
 * @package   Modules\Base\Controllers
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Modules\Base\Controllers;

use \Modules\Base\Models as M;
use \Application\Helpers as H;
use \Application\Helpers\DfDebug as dg;
use \Modules\Base\Models\DbTables as bDb;
use \Application\Helpers\Queues\Queue as Q;
use \Application\Assistance as A;
use \Config\CC as C;

class Cli extends A\Controller\CliController
{
    // ============================================================================
    // LOG FILE CONSTANTS
    // ============================================================================

    /** Log file for queue worker */
    const QUEUE_LOG_FILE = 'queue';

    /** Log file for database dumps */
    const DUMP_LOG_FILE = 'dump';

    /**
     * Constructor.
     *
     * @param A\Request $request Request object with CLI parameters
     */
    public function __construct(A\Request $request)
    {
        parent::__construct($request);
    }

    // ============================================================================
    // LIFETIME AND PERIOD CONSTANTS
    // ============================================================================

    /** Maximum runtime for a queue worker (3 hours) */
    const QUEUE_LIFETIME = 10800;

    /** Maximum age for log files before deletion (7 days) */
    const LOG_LIFETIME = 604800;

    /** Maximum age for dump files before deletion (3 days) */
    const DUMP_LIFETIME = 259200;

    /** Reschedule interval for database dumps (6 hours) */
    const DUMP_PERIOD = 21600;

    /** Base path for image storage */
    private $_pathPrefix = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . FOLDER_STORAGE . DIRECTORY_SEPARATOR . IMAGE_STORAGE . DIRECTORY_SEPARATOR;

    /**
     * Get the stack of recurring queue tasks.
     *
     * Each task is a closure that pushes a new job to the queue.
     *
     * @return array Task stack with action => closure
     */
    public function getStack()
    {
        return [
            Q::QUEUE_ACTION_DUMP => function ($v) {
                $queue = new Q();
                $queue->push(Q::QUEUE_ACTION_DUMP, $v, CURRENT_TIME + self::DUMP_PERIOD);
            },
        ];
    }

    /**
     * Daemon action: monitors and manages background processes.
     *
     * This method is called by cron every minute. It:
     * 1. Cleans old log files
     * 2. Checks for aborted queue processes
     * 3. Ensures recurring tasks are scheduled
     * 4. Restarts the queue worker if not running
     */
    public function demonAction()
    {
        // ====================================================================
        // LOG FILE CLEANUP
        // ====================================================================

        foreach ([
                     '',
                     API_LOG,
                     REST_API_LOG,
                     REPORT_DEBUG_LOG
                 ] as $path) {
            if (file_exists(DEBUG_PATH . $path)) {
                if ($logs = scandir(DEBUG_PATH . $path)) {
                    foreach ($logs as $v) {
                        if (preg_match("/([\d]{4}-[\d]{2}-[\d]{2})$/", $v)) {
                            if (strtotime(preg_replace("/^(.*?)([\d]{4}-[\d]{2}-[\d]{2})$/", "$2", $v)) < CURRENT_TIME - self::LOG_LIFETIME) {
                                if (unlink(DEBUG_PATH . $path . $v)) {
                                    dg::dflog("Remove log file " . DEBUG_PATH . $path . $v, 'demon', self::QUEUE_LOG_FILE, false, true);
                                } else {
                                    dg::dflog("\t\t\tCannot remove log file " . DEBUG_PATH . $path . $v, 'demon', self::QUEUE_LOG_FILE, false, true);
                                }
                            }
                        }
                    }
                }
            }
        }

        // ====================================================================
        // QUEUE PROCESS HEALTH CHECK
        // ====================================================================

        $stack = $this->getStack();
        $names = H\DfDebug::getQueueNames();

        if ($result = bDb\Queue::getByStack(false, bDb\Queue::QUEUE_STATUS_PROGRESS)) {
            $actions = H\DfDebug::getQueueNames();
            $pids = [];
            foreach ($result as $v) {
                if ($v->getQueueProcessId() > 0) {
                    $query = 'ps aux | grep ' . CONF_NAME . ' | grep ' . $v->getQueueProcessId() . ' | wc -l';
                    $count = exec($query, $code);
                    dg::dflog("Query '" . $query . "': result " . $count, 'server ' . C::get('queue_server'), \Modules\Base\Controllers\Cli::QUEUE_LOG_FILE, false, true);
                    if ($count < 2) {
                        $v->setQueueStatus(bDb\Queue::QUEUE_STATUS_ABORT)->save();
                        dg::dflog("Process " . $v->_id() . " (" . (isset($actions[$v->getQueueAction()]) ? $actions[$v->getQueueAction()] : 'Undefined action #' . $v->getQueueAction()) . ") aborted", 'server ' . C::get('queue_server'), \Modules\Base\Controllers\Cli::QUEUE_LOG_FILE, false, true);
                    } else {
                        $pids[] = $v->getQueueProcessId();
                    }
                }
            }
            dg::dflog("Found " . dfCount($pids) . " worked processes" . (dfCount($pids) > 0 ? ' ' . implode(',', $pids) : ''), 'server ' . C::get('queue_server'), \Modules\Base\Controllers\Cli::QUEUE_LOG_FILE, false, true);
        }

        // ====================================================================
        // RECURRING TASK SCHEDULING
        // ====================================================================

        $map = [];
        if ($res = bDb\Queue::getByStack(dfArrayKeys($stack), [
            bDb\Queue::QUEUE_STATUS_NEW,
            bDb\Queue::QUEUE_STATUS_PROGRESS
        ])
        ) {
            foreach ($res as $v) {
                $map[$v->getQueueAction()][] = $v;
            }
        }
        foreach ($stack as $k => $v) {
            if (!isset($map[$k])) {
                $stack[$k]([]);
                dg::dflog("Add queue by action #" . ' (' . $names[$k] . ')', 'demon', self::QUEUE_LOG_FILE, false, true);
            }
        }

        // ====================================================================
        // QUEUE WORKER RESTART
        // ====================================================================

        exec('ps aux | grep queue | grep "=' . CONF_NAME . ' "', $list);
        dg::dflog(dfJsonEncode($list), 'demon', self::QUEUE_LOG_FILE, false, true);
        foreach ($list as $v) {
            if (preg_match("/\-action\=queue/", $v)) {
                exit;
            }
        }
        dg::dflog("\n\n/***********************************************/\n/* Queue service not found: automatic restart. */\n/***********************************************/\n\n", 'demon', self::QUEUE_LOG_FILE, false, true);
        exec(C::get('console_php') . ' ' . ROOT_PATH . 'Application' . DIRECTORY_SEPARATOR . 'Tools' . DIRECTORY_SEPARATOR . 'cli.php -module=Base -controller=Cli -action=queue -stage=' . CONF_NAME . ' -request=0 &');
        exit;
    }

    /**
     * Set the process ID for a queue task.
     *
     * @param int $id Queue task ID
     *
     * @return M\Queue Queue model with updated PID
     */
    private function setPid($id)
    {
        $action = false;
        if ($id > 0) {
            $pid = getmypid();
            dg::dflog('Queue ' . $id . ' => pid ' . $pid, 'worker', self::QUEUE_LOG_FILE, false, true);
            $action = (bDb\Queue::getRow($id))->setQueueProcessId($pid);
            $action->save();
        }
        return $action;
    }

    /**
     * Main queue worker action.
     *
     * Runs in an infinite loop, processing new queue tasks.
     * Exits after QUEUE_LIFETIME seconds or when memory limit is reached.
     */
    public function queueAction()
    {
        $time = time();
        dg::dflog('init', 'worker', self::QUEUE_LOG_FILE, false, true);
        $queue = new Q();
        while (true) {
            try {
                $queue->getNew();
                if ((time() - $time) > self::QUEUE_LIFETIME) {
                    break;
                }
            } catch (\Exception $e) {
            }
            sleep(1);
        }
        dg::dflog("\tPeek usage memory: " . memory_get_peak_usage(true) . " b: restart", 'worker', self::QUEUE_LOG_FILE, false, true);
        exit;
    }

    /**
     * Database dump action.
     *
     * Creates a SQL dump of the main database using mysqldump.
     * Removes old dump files older than DUMP_LIFETIME.
     * Reschedules itself after completion.
     */
    public function dumpAction()
    {
        // Remove old dump files
        if (file_exists(DEBUG_PATH . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Dump' . DIRECTORY_SEPARATOR)) {
            if ($logs = scandir(DEBUG_PATH . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Dump' . DIRECTORY_SEPARATOR)) {
                foreach ($logs as $v) {
                    if (preg_match("/\.sql$/", $v)) {
                        if (strtotime(preg_replace("/^([\d]{4}\-[\d]{2}\-[\d]{2})-([\d]{2})\-([\d]{2})\-([\d]{2})\.sql/", '$1 $2:$3:$4', preg_replace("/^([^\d]{1,})/", '', $v))) < CURRENT_TIME - self::DUMP_LIFETIME) {
                            if (unlink(DEBUG_PATH . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Dump' . DIRECTORY_SEPARATOR . $v)) {
                                dg::dflog("Remove dump file " . DEBUG_PATH . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Dump' . DIRECTORY_SEPARATOR . $v, 'demon', self::QUEUE_LOG_FILE, false, true);
                            } else {
                                dg::dflog("\t\t\tCannot remove dump file " . DEBUG_PATH . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Dump' . DIRECTORY_SEPARATOR . $v, 'demon', self::QUEUE_LOG_FILE, false, true);
                            }
                        }
                    }
                }
            }
        }

        $_queue = $this->setPid($this->_request->getParams());
        $dumpExclude = [
            //bDb\PersonLog::$_table,
            //            gDb\Country::$_table,
            //            gDb\State::$_table
        ];
        $base = C::get(C::get()->main_database);
        dg::dflog('Dump "' . $base->db_name . '"' . (dfCount($dumpExclude) > 0 ? ' without tables ' . implode(', ', $dumpExclude) : ''), 'start', self::DUMP_LOG_FILE, false, true);
        $ignore = '';
        if (dfCount($dumpExclude) > 0) {
            foreach ($dumpExclude as $v) {
                $ignore .= ' --ignore-table=' . $base->db_name . '.' . $v;
            }
        }
        $name = 'dump_' . $base->db_name . '_' . date('Y-m-d-H-i-s') . '.sql';
//        dg::dflog('mysqldump -u ' . $base->db_login-> . ' -p****** --host=' . $base->db_host . ' --port=' . $base->db_port . ' ' . $base->db_name . $ignore . ' > ' . ROOT_PATH . 'Config' . DIRECTORY_SEPARATOR . 'dump' . DIRECTORY_SEPARATOR . $name, 'prepare', self::DUMP_LOG_FILE, false, true);
        @exec('mysqldump -u ' . $base->db_login . ' -p' . $base->db_password . ' --host=' . $base->db_host . ' --port=' . $base->db_port . ' ' . $base->db_name . $ignore . ' > ' . ROOT_PATH . 'Config' . DIRECTORY_SEPARATOR . 'dump' . DIRECTORY_SEPARATOR . $name);
        dg::dflog('Dump created', 'finish', self::DUMP_LOG_FILE, false, true);
        $_queue->setQueueStatus(bDb\Queue::QUEUE_STATUS_SUCCESS)->save();
        $this->getStack()[Q::QUEUE_ACTION_DUMP]([]);
    }
}