<?php

namespace Modules\Base\Models;

/**
 * Entity model class.
 *
 * Represents a generic entity reference. Provides a unified way to
 * store and retrieve entity IDs and their associated values across
 * different table types.
 *
 * @package   Modules\Base\Models
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method int getEntityId()
 * @method $this setEntityId(int $entity_id)
 * @method int getEntityEntity()
 * @method $this setEntityEntity(int $entity_entity)
 * @method string getEntityEntityType()
 * @method $this setEntityEntityType(string $entity_entity_type)
 * @method string getEntityValue()
 * @method $this setEntityValue(string $entity_value)
 */
class Entity extends \Application\Assistance\Model
{
    /** @var int Primary key */
    public $entity_id;

    /** @var int Entity ID (reference to the actual record) */
    public $entity_entity;

    /** @var string Entity type (table name or class identifier) */
    public $entity_entity_type;

    /** @var string Entity value (stored value or hash) */
    public $entity_value;
}