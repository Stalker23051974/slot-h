<?php

namespace Modules\Base\Models\DbTables;

/**
 * Entity database table class.
 *
 * Manages entity references and unique identifiers for various records.
 * Provides a unified way to store and retrieve entity IDs and their
 * associated values across different table types.
 *
 * Features:
 * - Generic entity storage with type discrimination
 * - Value-based lookup
 * - Entity-type pairs for polymorphic relationships
 *
 * @package   Modules\Base\Models\DbTables
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method static \Modules\Base\Models\Entity|array|false getRow($ids = null, $page = false, $order = false, $model = true, $cache = true)
 * @method static \Modules\Base\Models\Entity[]|array|false getAll($page = false, $order = false, $model = true)
 */
class Entity extends \Application\Assistance\DatabaseNormal
{
    /** Primary key field */
    const ENTITY_ID = 'entity_id';

    /** Entity ID (reference to the actual record) */
    const ENTITY_ENTITY = 'entity_entity';

    /** Entity type (table name or class identifier) */
    const ENTITY_ENTITY_TYPE = 'entity_entity_type';

    /** Entity value (stored value or hash) */
    const ENTITY_VALUE = 'entity_value';

    /** Table name */
    public static $_table = 'entities';

    /** Primary key field */
    public static $_index = self::ENTITY_ID;

    /**
     * Field definitions.
     *
     * @var array
     */
    public static $_fields = [
        self::ENTITY_ID => [self::FP_INDEX => true],
        self::ENTITY_ENTITY => [],
        self::ENTITY_ENTITY_TYPE => [self::FP_TYPE => self::TYPE_STRING],
        self::ENTITY_VALUE => [self::FP_TYPE => self::TYPE_STRING],
    ];

    /**
     * Get an entity record by entity ID and type.
     *
     * @param int    $entity Entity ID to look up
     * @param string $type   Entity type (table name)
     *
     * @return \Modules\Base\Models\Entity|false Entity model or false if not found
     */
    public static function getByEntity($entity, $type)
    {
        return (static::getSelect())
            ->addWhere([
                static::createWhere(self::ENTITY_ENTITY, $entity),
                static::createWhere(self::ENTITY_ENTITY_TYPE, $type)
            ])
            ->pop()
            ->result();
    }
}