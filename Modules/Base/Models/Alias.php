<?php

namespace Modules\Base\Models;

/**
 * Alias model class.
 *
 * Represents a URL alias record. Provides methods for accessing
 * and manipulating alias data.
 *
 * @package   Modules\Base\Models
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method int getAliasId()
 * @method $this setAliasId(int $alias_id)
 * @method string getAliasLink()
 * @method $this setAliasLink(string $alias_link)
 * @method string getAliasValue()
 * @method $this setAliasValue(string $alias_value)
 * @method string getAliasName()
 * @method $this setAliasName(string $alias_name)
 * @method int getAliasType()
 * @method $this setAliasType(int $alias_type)
 * @method int getProjectId()
 * @method $this setProjectId(int $project_id)
 *
 * @method Project|false linkProjectId()
 */
class Alias extends \Application\Assistance\Model
{
    /** @var int Primary key */
    public $alias_id;

    /** @var string URL path alias */
    public $alias_link;

    /** @var string Internal target path */
    public $alias_value;

    /** @var string Display name */
    public $alias_name;

    /** @var int Alias type */
    public $alias_type;

    /** @var int Project ID */
    public $project_id;
}