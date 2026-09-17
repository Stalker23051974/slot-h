<?php

/**
 * Database helper with extended functionality.
 *
 * This class provides additional database operations beyond the base ORM:
 * - List generation with caching and templates
 * - Autocomplete data formatting (countries, states, cities)
 * - Boolean value normalization
 * - Dynamic relationship queries
 * - Many-to-many relationship management
 * - Record duplication with field overrides
 *
 * Extends DatabaseCache to leverage caching for list operations.
 *
 * @package   Application\Helpers
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Helpers;

use \Application\Assistance\DatabaseCache as Cache;
use \Modules\Base\Models\DbTables as db;

class Database extends \Application\Assistance\DatabaseCache
{
    // ============================================================================
    // METHOD PREFIX CONSTANTS
    // ============================================================================

    /** Prefix for getter methods (getFieldName) */
    const PREFIX_GET = 'get';

    /** Prefix for setter methods (setFieldName) */
    const PREFIX_SET = 'set';

    /** Prefix for link methods (linkFieldName) - one-to-one relationships */
    const PREFIX_LINK = 'link';

    /** Prefix for collection methods (lotsFieldInModuleByField) - one-to-many */
    const PREFIX_LOTS = 'lots';

    /** Prefix for reverse relationship methods (relatedFieldInModuleByField) */
    const PREFIX_RELATED = 'related';

    // ============================================================================
    // AUTOCOMPLETE (ACMPT) CONSTANTS
    // ============================================================================

    /** Autocomplete type: town/city search */
    const ACMPT_TYPE_TOWN = 0;

    /** Town field: country name */
    const ACMPT_TOWN_COUNTRY = 'country';

    /** Town field: country ID */
    const ACMPT_TOWN_COUNTRY_ID = 'country_id';

    /** Town field: language ID */
    const ACMPT_TOWN_LANGUAGE_ID = 'language_id';

    /** Town field: state/region name */
    const ACMPT_TOWN_STATE = 'state';

    /** Town field: state/region ID */
    const ACMPT_TOWN_STATE_ID = 'state_id';

    /** Town field: city name */
    const ACMPT_TOWN_CITY = 'city';

    /** Town field: city ID */
    const ACMPT_TOWN_CITY_ID = 'city_id';

    /** Town field: postal/index code */
    const ACMPT_TOWN_POST_INDEX = 'index';

    /** Autocomplete type: state/region */
    const ACMPT_TYPE_STATE = 2;

    /** State field: ID */
    const ACMPT_STATE_ID = 'id';

    /** State field: name */
    const ACMPT_STATE_NAME = 'name';

    /**
     * Get full list (including soft-deleted records) with caching.
     *
     * @param string     $module   Module name
     * @param string     $entity   Entity/DbTable class name
     * @param bool|string $template List template identifier
     * @param array      $ids      Record IDs
     *
     * @return array|bool|false|object List data
     */
    public static function getFullList($module, $entity, $template = false, $ids = [])
    {
        if (false === $template) {
            $template = Database::LIST_TEMPLATE_BASE;
        }

        if ($response = parent::getList($module, $entity, $template, $ids)) {
            return $response;
        }

        /** @var \Application\Assistance\Database $db */
        $db = '\\' . \Application\Crud::MODULE_FOLDER . '\\' . $module
            . '\\' . \Application\Crud::MODEL_FOLDER . '\\'
            . \Application\Crud::DATABASE_FOLDER . '\\' . $entity;

        // Include soft-deleted records
        $db::setIgnoreExtend(true);
        return static::listLogic($db, $template, $ids);
    }

    /**
     * Get list (active records only) with caching.
     *
     * @param string     $module   Module name
     * @param string     $entity   Entity/DbTable class name
     * @param bool|string $template List template identifier
     * @param array      $ids      Record IDs
     *
     * @return array|bool|false|object List data
     */
    public static function getList($module, $entity, $template = false, $ids = [])
    {
        if (false === $template) {
            $template = Database::LIST_TEMPLATE_BASE;
        }

        if ($response = parent::getList($module, $entity, $template, $ids)) {
            return $response;
        }

        /** @var \Application\Assistance\Database $db */
        $db = '\\' . \Application\Crud::MODULE_FOLDER . '\\' . $module
            . '\\' . \Application\Crud::MODEL_FOLDER . '\\'
            . \Application\Crud::DATABASE_FOLDER . '\\' . $entity;

        return static::listLogic($db, $template, $ids);
    }

    /**
     * Core list generation logic with caching.
     *
     * Builds a formatted list from database records using a template
     * that defines which fields to display.
     *
     * @param \Application\Assistance\Database $db       Database class
     * @param string                           $template Template identifier
     * @param array                            $ids      Record IDs
     *
     * @return array Formatted list with values and attributes
     */
    protected static function listLogic($db, $template, $ids)
    {
        $res = static::getByKey($db::$_table . ':' . Cache::$_listPart . ':' . $template);
        if (is_array($res) && !empty($res)) {
            return $res;
        }

        $listTemplate = $db::getListTemplate($template);
        /** @var array|int $value */
        $value = $listTemplate['value'];

        $res = empty($ids)
            ? $db::getAll(false, [(!is_array($value) ? $value : $value[0]) . ' ' . \Application\Assistance\Select::SORT_ASC], false)
            : $db::getRow($ids, false, [(!is_array($value) ? $value : $value[0]) . ' ' . \Application\Assistance\Select::SORT_ASC], false);

        if (false === $res) {
            return [];
        }

        $response = [];
        $attribs = array_flip(dfArrayKeys($listTemplate));
        unset($attribs['id'], $attribs['value']);
        $attribs = dfArrayKeys($attribs);

        foreach ($res as $v) {
            $response[$v[$listTemplate['id']]] = [
                'value' => !is_array($value)
                    ? $v[$value]
                    : preg_replace("/[\s]{2,}/", ' ', implode(' ', dfArrayMap(function ($v1) use ($v) {
                        return isset($v[$v1]) ? $v[$v1] : $v1;
                    }, $value))),
                'attribs' => []
            ];

            if (!empty($attribs)) {
                foreach ($attribs as $attr) {
                    $response[$v[$listTemplate['id']]]['attribs'][$attr] = $v[$listTemplate[$attr]];
                }
            }
        }

        if (!empty($response)) {
            static::setCacheValue(implode(Cache::$_keySeparator, [
                $db::$_table,
                Cache::$_listPart,
                $template
            ]), $response);
        }

        return $response;
    }

    /**
     * Format data for autocomplete suggestions.
     *
     * @param array $arr  Raw data array
     * @param bool  $type Data type (ACMPT_TYPE_TOWN or ACMPT_TYPE_STATE)
     *
     * @return array Formatted autocomplete data
     */
    public static function generateAcmptList($arr, $type = false)
    {
        if (false === $type) {
            $type = self::ACMPT_TYPE_TOWN;
        }

        switch ($type) {
            case self::ACMPT_TYPE_TOWN:
                return [
                    'value' => $arr[self::ACMPT_TOWN_CITY] . ' ('
                        . mb_strtoupper($arr[self::ACMPT_TOWN_COUNTRY])
                        . ($arr[self::ACMPT_TOWN_STATE] != '' ? ', ' . $arr[self::ACMPT_TOWN_STATE] : '')
                        . ')',
                    'data' => $arr[self::ACMPT_TOWN_CITY_ID],
                    'state' => $arr[self::ACMPT_TOWN_STATE_ID],
                    'stateName' => $arr[self::ACMPT_TOWN_STATE],
                    'country' => $arr[self::ACMPT_TOWN_COUNTRY_ID],
                    'language' => $arr[self::ACMPT_TOWN_LANGUAGE_ID],
                    'indexes' => $arr[self::ACMPT_TOWN_POST_INDEX],
                ];
            default:
                return $arr;
        }
    }

    /**
     * Normalize a value to boolean.
     *
     * Accepts true, 1, '1', 't' as true values.
     * All other values are false.
     *
     * @param mixed $v Input value
     *
     * @return bool Boolean representation
     */
    public static function checkBoolean($v)
    {
        return true === $v || 1 == $v || '1' == $v || 't' == $v;
    }

    /**
     * Dynamically fetch related records.
     *
     * Used by Model's magic methods for relationships:
     * - lotsFieldInModuleByField: one-to-many
     * - relatedFieldInModuleByField: reverse one-to-many
     *
     * @param \Application\Assistance\Database $class Target database class
     * @param string                           $field Field name for the relationship
     * @param mixed                            $value Value to match
     * @param bool                             $multi Whether to return multiple results
     *
     * @return bool|mixed Single model or array of models
     */
    public static function getDinamicly($class, $field, $value, $multi = false)
    {
        if ($value > 0) {
            static::$_class = '';
            ($class::getSelect())->addWhere($class::createWhere($field, $value));

            if (!$result = $class::getByCondition(false, false, false)) {
                return false;
            }
            return true !== $multi ? array_pop($result) : $result;
        }
        return false;
    }

    /**
     * Update many-to-many relationships.
     *
     * Compares current relations with the desired list and:
     * - Removes relations not in the new list
     * - Adds missing relations
     *
     * @param \Application\Assistance\Database $target   Target database class
     * @param string                           $field    Field for the relationship
     * @param int                              $parent   Parent record ID
     * @param string                           $relation Related field name
     * @param array                            $list     Desired relation IDs
     *
     * @return bool Whether any changes were made
     */
    public static function updateRelations($target, $field, $parent, $relation, $list)
    {
        $ids = [];
        $primary = $target::$_index;
        $model = preg_replace("/" . \Application\Crud::DATABASE_FOLDER . "\\\\" . "/", '', $target);
        $modelRes = new $model();

        $primaryMethod = $modelRes->_methodList[$primary];
        $relation = $modelRes->_methodList[$relation];
        $field = $modelRes->_methodList[$field];

        if ($relationRes = $target::{self::PREFIX_GET . 'By' . $field}($parent)) {
            dfArrayMap(function ($v) use (&$ids, $relation, $primaryMethod) {
                $ids[$v->{self::PREFIX_GET . $relation}()] = $v->{self::PREFIX_GET . $primaryMethod}();
            }, $relationRes);
        }

        $update = false;
        $remove = dfArrayDiff(dfArrayKeys($ids), $list);
        $insert = dfArrayDiff($list, dfArrayKeys($ids));

        // Remove relations not in the new list
        if (dfCount($remove) > 0) {
            $update = true;
            $list = [];
            foreach ($remove as $v) {
                $list[] = $ids[$v];
            }
            $target::remove([$primary => $list]);
        }

        // Add new relations
        if (dfCount($insert) > 0) {
            $update = true;
            foreach ($insert as $v) {
                (new $model)
                    ->{self::PREFIX_SET . $field}($parent)
                    ->{self::PREFIX_SET . $relation}($v)
                    ->save();
            }
        }

        return $update;
    }

    /**
     * Duplicate a record with optional field overrides.
     *
     * Creates a new record copying all fields except:
     * - Primary key (auto-generated)
     * - updated_at (auto-generated)
     * - deleted_at (starts as null)
     *
     * @param \Application\Assistance\Model $object Original model
     * @param array                         $change Field overrides (field => value)
     *
     * @return \Application\Assistance\Model New model instance
     */
    public static function duplicate($object, $change = [])
    {
        $class = get_class($object);
        $model = new $class();
        $db = $object->_db;
        /** @var \Application\Assistance\Model $model */
        /** @var \Application\Assistance\Database $db */

        foreach ($db::getFields() as $k => $v) {
            if (!dfInArray($k, [
                $db::$_index,
                \Application\Assistance\DatabaseExtend::FIELD_UPDATED_AT,
                \Application\Assistance\DatabaseExtend::FIELD_DELETED_AT
            ])) {
                $model->{self::PREFIX_SET . $model->_methodList[$k]}(
                    isset($change[$k]) ? $change[$k] : $object->{self::PREFIX_GET . $model->_methodList[$k]}()
                );
            }
        }

        $model->save();
        return $model;
    }

    /**
     * Get language-specific relationship counts.
     *
     * @param int $langId Language ID
     *
     * @return array Language relation statistics
     */
    public static function getLanguageRelations($langId)
    {
        return [
            'Users' => [
                'count' => db\Person::relatedByLanguage($langId),
                'controller' => false
            ],
        ];
    }
}