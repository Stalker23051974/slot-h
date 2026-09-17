<?php

namespace Modules\Base\Models;

/**
 * ProjectLanguage model class.
 *
 * Represents the many-to-many relationship between projects and languages.
 *
 * @package   Modules\Base\Models
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method int getProjectLanguageId()
 * @method $this setProjectLanguageId(int $project_language_id)
 * @method int getLanguageId()
 * @method $this setLanguageId(int $language_id)
 * @method int getProjectId()
 * @method $this setProjectId(int $project_id)
 *
 * @method Project|false linkProjectId()
 * @method \Modules\Geo\Models\Language|false linkLanguageId()
 */
class ProjectLanguage extends \Application\Assistance\Model
{
    /** @var int Primary key */
    public $project_language_id;

    /** @var int Language ID */
    public $language_id;

    /** @var int Project ID */
    public $project_id;
}