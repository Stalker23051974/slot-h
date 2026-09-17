<?php

namespace Modules\Base\Models;

/**
 * PersonLog model class.
 *
 * Represents a user activity log record. Tracks all user actions
 * for audit and monitoring purposes.
 *
 * @package   Modules\Base\Models
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method int getPersonLogId()
 * @method $this setPersonLogId(int $person_log_id)
 * @method int getPersonId()
 * @method $this setPersonId(int $person_id)
 * @method string getPersonLogName()
 * @method $this setPersonLogName(string $person_log_name)
 * @method int getPersonLogIp()
 * @method $this setPersonLogIp(int $person_log_ip)
 * @method int getPersonLogTime()
 * @method $this setPersonLogTime(int $person_log_time)
 * @method int getPersonLogModuleCode()
 * @method $this setPersonLogModuleCode(int $person_log_module_code)
 * @method int getPersonLogControllerCode()
 * @method $this setPersonLogControllerCode(int $person_log_controller_code)
 * @method string getPersonLogModule()
 * @method $this setPersonLogModule(string $person_log_module)
 * @method string getPersonLogController()
 * @method $this setPersonLogController(string $person_log_controller)
 * @method string getPersonLogAction()
 * @method $this setPersonLogAction(string $person_log_action)
 * @method int getPersonLogEntity()
 * @method $this setPersonLogEntity(int $person_log_entity)
 * @method string getPersonLogChange()
 * @method $this setPersonLogChange(string $person_log_change)
 * @method int getPersonLogArchive()
 * @method $this setPersonLogArchive(int $person_log_archive)
 *
 * @method Person|false linkPersonId()
 */
class PersonLog extends \Application\Assistance\Model
{
    /** @var int Primary key */
    public $person_log_id;

    /** @var int Person ID */
    public $person_id;

    /** @var string Person name (denormalized) */
    public $person_log_name;

    /** @var int IP address (stored as long) */
    public $person_log_ip;

    /** @var int Action timestamp */
    public $person_log_time;

    /** @var int Module code */
    public $person_log_module_code;

    /** @var int Controller code */
    public $person_log_controller_code;

    /** @var string Module name */
    public $person_log_module;

    /** @var string Controller name */
    public $person_log_controller;

    /** @var string Action name */
    public $person_log_action;

    /** @var int Entity ID */
    public $person_log_entity;

    /** @var string Change description */
    public $person_log_change;

    /** @var int Archive flag */
    public $person_log_archive;
}