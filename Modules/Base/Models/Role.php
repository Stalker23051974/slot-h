<?php

namespace Modules\Base\Models;

/**
 * Role model class.
 *
 * Represents a user role record. Roles define permission sets
 * and can have hierarchical relationships.
 *
 * @package   Modules\Base\Models
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method int getRoleId()
 * @method $this setRoleId(int $role_id)
 * @method string getRoleName()
 * @method $this setRoleName(string $role_name)
 * @method int getParent()
 * @method $this setParent(int $parent)
 * @method string getRoleData()
 * @method $this setRoleData(string $role_data)
 * @method int getProjectId()
 * @method $this setProjectId(int $project_id)
 * @method int getRoleInternal()
 * @method $this setRoleInternal(int $role_internal)
 *
 * @method Role|false linkParent()
 * @method Project|false linkProjectId()
 */
class Role extends \Application\Assistance\Model
{
    /** @var int Primary key */
    public $role_id;

    /** @var string Role name */
    public $role_name;

    /** @var int Parent role ID */
    public $parent;

    /** @var string Role data (JSON) */
    public $role_data;

    /** @var int Project ID */
    public $project_id;

    /** @var int Internal role flag */
    public $role_internal;

    /** JSON data field name */
    protected $_dataField = 'RoleData';
}