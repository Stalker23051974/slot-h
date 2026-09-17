<?php

namespace Modules\Base\Models;

/**
 * AuthCheck model class.
 *
 * Represents an authentication attempt record. Used for brute force
 * protection and login attempt tracking.
 *
 * @package   Modules\Base\Models
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method int getAuthCheckId()
 * @method $this setAuthCheckId(int $auth_check_id)
 * @method int getAuthCheckTime()
 * @method $this setAuthCheckTime(int $auth_check_time)
 * @method string getAuthCheckHash()
 * @method $this setAuthCheckHash(string $auth_check_hash)
 * @method int getAuthCheckAttempt()
 * @method $this setAuthCheckAttempt(int $auth_check_attempt)
 */
class AuthCheck extends \Application\Assistance\Model
{
    /** @var int Primary key */
    public $auth_check_id;

    /** @var int Attempt timestamp */
    public $auth_check_time;

    /** @var string Server fingerprint hash */
    public $auth_check_hash;

    /** @var int Number of attempts */
    public $auth_check_attempt;
}