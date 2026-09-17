<?php

namespace Modules\Base\Models;

/**
 * Project model class.
 *
 * Represents a project/tenant record. Each project is a separate
 * website or application instance sharing the same codebase.
 *
 * @package   Modules\Base\Models
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method int getProjectId()
 * @method $this setProjectId(int $project_id)
 * @method string getProjectName()
 * @method $this setProjectName(string $project_name)
 * @method string getProjectLanding()
 * @method $this setProjectLanding(string $project_landing)
 * @method string getProjectData()
 * @method $this setProjectData(string $project_data)
 * @method int getTimezoneId()
 * @method $this setTimezoneId(int $timezone_id)
 *
 * @method \Modules\Geo\Models\Timezone linkTimezoneId()
 */
class Project extends \Application\Assistance\Model
{
    /** @var int Primary key */
    public $project_id;

    /** @var string Project name */
    public $project_name;

    /** @var string Landing page URL */
    public $project_landing;

    /** @var string Project data (JSON) */
    public $project_data;

    /** @var int Timezone ID */
    public $timezone_id;

    /** JSON data field name for project data */
    public $_dataField = 'ProjectData';
}