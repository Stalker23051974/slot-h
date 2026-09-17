<?php

namespace Modules\Base\Models;

/**
 * Constant model class.
 *
 * Represents a system or user-defined constant. Constants provide
 * dynamic configuration values that can be changed at runtime.
 *
 * @package   Modules\Base\Models
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method int getConstantId()
 * @method $this setConstantId(int $constant_id)
 * @method string getConstantValue()
 * @method $this setConstantValue(string $constant_value)
 * @method string getConstantName()
 * @method $this setConstantName(string $constant_name)
 * @method int getConstantType()
 * @method $this setConstantType(int $constant_type)
 * @method string getConstantDescription()
 * @method $this setConstantDescription(string $constant_description)
 * @method string getConstantData()
 * @method $this setConstantData(string $constant_data)
 * @method int getConstantGroup()
 * @method $this setConstantGroup(int $constant_group)
 * @method int getProjectId()
 * @method $this setProjectId(int $project_id)
 *
 * @method \Modules\Base\Models\Project linkProjectId()
 */
class Constant extends \Application\Assistance\Model
{
    /** @var int Primary key */
    public $constant_id;

    /** @var string Constant value */
    public $constant_value;

    /** @var string Constant name (identifier) */
    public $constant_name;

    /** @var int Constant type (system, editable) */
    public $constant_type;

    /** @var string Constant description */
    public $constant_description;

    /** @var string Additional data (JSON) */
    public $constant_data;

    /** @var int Constant group */
    public $constant_group;

    /** @var int Project ID */
    public $project_id;

    /** JSON data field name for protected data */
    public $_dataField = 'ConstantData';
}