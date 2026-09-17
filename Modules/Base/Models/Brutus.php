<?php

namespace Modules\Base\Models;

use \Application\Helpers\DfDebug as dg;

/**
 * Brutus model class.
 *
 * Represents a brute force attack attempt record. Used for security
 * monitoring and blocking suspicious access attempts.
 *
 * @package   Modules\Base\Models
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method int getBrutusId()
 * @method $this setBrutusId(int $brutus_id)
 * @method string getBrutusUrl()
 * @method $this setBrutusUrl(string $brutus_url)
 * @method int getBrutusCounter()
 * @method $this setBrutusCounter(int $brutus_counter)
 * @method string getBrutusDomen()
 * @method $this setBrutusDomen(string $brutus_domen)
 */
class Brutus extends \Application\Assistance\Model
{
    /** @var int Primary key */
    public $brutus_id;

    /** @var string Accessed URL */
    public $brutus_url;

    /** @var int Attempt counter */
    public $brutus_counter;

    /** @var string Domain name */
    public $brutus_domen;

    /** Log file name for brute force attempts */
    const BRUTUS_LOG = 'brutus_log';

    /**
     * Increment the attempt counter and log the incident.
     *
     * @return $this
     */
    public function increment()
    {
        $this->setBrutusCounter((int)($this->getBrutusCounter()) + 1)->save();

        dg::dflog(
            dfJsonEncode([
                'url' => $this->getBrutusUrl(),
                'get' => $_GET,
                'post' => $_POST,
                'files' => $_FILES
            ]),
            $_SERVER['REMOTE_ADDR'],
            CURRENT_PROJECT . '_' . self::BRUTUS_LOG,
            false,
            true
        );

        return $this;
    }
}