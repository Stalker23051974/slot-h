<?php

namespace Modules\Base\Models\DbTables;

use Application\Assistance\Select;
use Application\Assistance\Select\Where;
use Application\Helpers\Database;
use \Application\Helpers\DfDebug as dg;
use \Config\CC as C;

/**
 * Queue database table class.
 *
 * Manages background task queue for asynchronous processing.
 * This is the core table for the queue server system.
 *
 * Features:
 * - Task status tracking (new, progress, success, error, abort)
 * - Task parameters storage (JSON)
 * - Process ID tracking for monitoring
 * - Time-based scheduling
 * - Queue throttling (max concurrent tasks)
 * - Statistics and monitoring
 *
 * @package   Modules\Base\Models\DbTables
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method static \Modules\Base\Models\Queue|array|false getRow($ids = null, $page = false, $order = false, $model = true, $cache = true)
 * @method static \Modules\Base\Models\Queue[]|array|false getAll($page = false, $order = false, $model = true)
 */
class Queue extends \Application\Assistance\DatabaseNormal
{
    /** Primary key field */
    const QUEUE_ID = 'queue_id';

    /** Task status (new, progress, success, error, abort) */
    const QUEUE_STATUS = 'queue_status';

    /** Task parameters (JSON) */
    const QUEUE_PARAMS = 'queue_params';

    /** Task action/type */
    const QUEUE_ACTION = 'queue_action';

    /** Scheduled execution time */
    const QUEUE_TIME = 'queue_time';

    /** First start timestamp */
    const QUEUE_FIRST_START = 'queue_first_start';

    /** Process ID of the running task */
    const QUEUE_PROCESS_ID = 'queue_process_id';

    /** Table name */
    public static $_table = 'queues';

    /** Primary key field */
    public static $_index = self::QUEUE_ID;

    /**
     * Field definitions.
     *
     * @var array
     */
    public static $_fields = [
        self::QUEUE_ID => [self::FP_INDEX => true],
        self::QUEUE_STATUS => [self::FP_INDEX => true],
        self::QUEUE_PARAMS => [self::FP_TYPE => self::TYPE_TEXT, self::FP_NULL => true],
        self::QUEUE_ACTION => [self::FP_INDEX => true],
        self::QUEUE_TIME => [self::FP_INDEX => true],
        self::QUEUE_FIRST_START => [self::FP_NULL => true],
        self::QUEUE_PROCESS_ID => [self::FP_NULL => true]
    ];

    /** Maximum tasks to process in one batch */
    const QUEUE_CHANK = 10;

    /** Maximum concurrent tasks */
    const QUEUE_THROWS = 10;

    // ============================================================================
    // QUEUE STATUS CONSTANTS
    // ============================================================================

    /** New task, waiting for processing */
    const QUEUE_STATUS_NEW = 1;

    /** Task is currently being processed */
    const QUEUE_STATUS_PROGRESS = 2;

    /** Task completed successfully */
    const QUEUE_STATUS_SUCCESS = 3;

    /** Task failed with error */
    const QUEUE_STATUS_ERROR = 4;

    /** Task was force aborted */
    const QUEUE_STATUS_ABORT = 5;

    /** Task is deprecated/outdated */
    const QUEUE_STATUS_DEPRECATED = 6;

    /** Task is disabled */
    const QUEUE_STATUS_DISABLED = 7;

    /**
     * Get localized status descriptions.
     *
     * @return array Status => description
     */
    public static function getStatuses()
    {
        return [
            self::QUEUE_STATUS_NEW => C::locale('New task'),
            self::QUEUE_STATUS_PROGRESS => C::locale('In the process'),
            self::QUEUE_STATUS_SUCCESS => C::locale('Successfully completed'),
            self::QUEUE_STATUS_ERROR => C::locale('Runtime error'),
            self::QUEUE_STATUS_ABORT => C::locale('Force stopped'),
            self::QUEUE_STATUS_DEPRECATED => C::locale('Outdated data form'),
            self::QUEUE_STATUS_DISABLED => C::locale('Disabled'),
        ];
    }

    /**
     * Get active statuses for monitoring.
     *
     * @return array
     */
    public static function getActiveStatuses()
    {
        return [
            self::QUEUE_STATUS_PROGRESS,
            self::QUEUE_STATUS_ERROR,
            self::QUEUE_STATUS_ABORT,
            self::QUEUE_STATUS_DEPRECATED
        ];
    }

    /**
     * Get new tasks ready for processing.
     *
     * Limits concurrent tasks to QUEUE_THROWS.
     *
     * @return \Modules\Base\Models\Queue[]|false
     */
    public static function getNew()
    {
        $total = (static::getSelect(Select::COUNT))
            ->addWhere(static::createWhere(self::QUEUE_STATUS, self::QUEUE_STATUS_PROGRESS))
            ->cache(false)
            ->result();

        $offset = self::QUEUE_THROWS - $total;

        if ($offset > 0) {
            $res = (static::getSelect())
                ->addWhere([
                    static::createWhere(self::QUEUE_STATUS, self::QUEUE_STATUS_NEW),
                    static::createWhere(self::QUEUE_TIME, time(), Where::LESS_EQUAL)
                ])
                ->result();

            dg::dflog(
                'Offset = ' . $offset . ', get ' . (false === $res ? 0 : dfCount($res)) . ' rows (total: ' . $total . ').',
                'server ' . C::get('queue_server'),
                \Modules\Base\Controllers\Cli::QUEUE_LOG_FILE,
                false,
                true
            );
            return $res;
        }

        dg::dflog('New rows not found', 'server ' . C::get('queue_server'), \Modules\Base\Controllers\Cli::QUEUE_LOG_FILE, false, true);
        return false;
    }

    /**
     * Get queue statistics grouped by action and status.
     *
     * @return \Modules\Base\Models\Queue[]|false
     */
    public static function getStatistic()
    {
        self::setModelResponse(false);
        return (self::getSelect())
            ->setFields([
                self::QUEUE_ACTION,
                self::QUEUE_STATUS,
                (new Select\FieldHandler())->setMin()->setField(self::QUEUE_TIME)->setAlias('min_queue'),
                (new Select\FieldHandler())->setMax()->setField(self::QUEUE_TIME)->setAlias('last_queue'),
                (new Select\FieldHandler())->setCount()->setAlias(Database::$_countField)
            ])
            ->setGroups([self::QUEUE_ACTION, self::QUEUE_STATUS])
            ->addWhere(static::createWhere(self::QUEUE_STATUS, null, Where::NOT_NULL))
            ->result();
    }

    /**
     * Get tasks by stack (action IDs) and status.
     *
     * @param array|int|bool $ids    Action ID(s)
     * @param int|array      $status Status(es)
     * @param bool|int       $time   Time filter
     *
     * @return \Modules\Base\Models\Queue[]|false
     */
    public static function getByStack($ids = false, $status = false, $time = false)
    {
        return (false !== $ids || false !== $status)
            ? (self::getSelect())
                ->addWhere([
                    static::createWhere(self::QUEUE_STATUS, $status),
                    false !== $ids ? static::createWhere(self::QUEUE_ACTION, $ids) : null,
                    false !== $time ? static::createWhere(self::QUEUE_TIME, $time, Where::LESS_EQUAL) : null
                ])
                ->result()
            : false;
    }

    /**
     * Get active tasks by type/action.
     *
     * @param int $type Action type
     *
     * @return \Modules\Base\Models\Queue[]|false
     */
    public static function getActiveByType($type)
    {
        return (self::getSelect())
            ->addWhere([
                static::createWhere(self::QUEUE_STATUS, self::QUEUE_STATUS_NEW),
                static::createWhere(self::QUEUE_ACTION, $type)
            ])
            ->result();
    }
}