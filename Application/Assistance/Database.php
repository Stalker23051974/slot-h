<?php

/**
 * Database abstraction layer and ORM base class.
 *
 * This is the core database class that provides:
 * - Object-Relational Mapping (ORM) for database tables
 * - Query building via Select objects
 * - CRUD operations (Create, Read, Update, Delete)
 * - Caching (static and external cache engines)
 * - Localization/translation support
 * - Project isolation and status filtering
 * - Field type mapping and query optimization
 *
 * Each database table has a corresponding class that extends Database
 * or one of its specialized descendants:
 * - DatabaseNormal: Standard table with no special fields
 * - DatabaseExtend: Tables with updated_at and deleted_at
 * - DatabaseNormalProject: Tables with project_id
 * - DatabaseExtendProject: Tables with both extend fields and project_id
 * - DatabaseNormalStatus: Tables with status field
 * - DatabaseExtendStatus: Tables with both extend fields and status
 *
 * @package   Application\Assistance
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Assistance;

use \Application\Assistance\Select as selectObj;
use \Application\Assistance\Select\Where as Where;
use \Config\CC as C;
use function Config\showCaller;

class Database
{
    // ============================================================================
    // LIST TEMPLATE CONSTANTS
    // ============================================================================

    /** Base list template identifier */
    const LIST_TEMPLATE_BASE = 0;

    // ============================================================================
    // PROPERTIES
    // ============================================================================

    /** @var string Table name in the database */
    public static $_table;

    /** @var string Primary key/index field name */
    public static $_index = '';

    /** @var \Application\Assistance\Select Current SELECT object */
    protected static $_select;

    /** @var string|null Database name (null = use default from config) */
    protected static $_base = null;

    /** @var string|null Database block/configuration name */
    public static $_baseBlock = null;

    /** @var array Field definitions (name => type, index, null, etc.) */
    public static $_fields = [];

    /** @var array Fields to log changes (field => display name) */
    protected static $_fieldsToLog = [];

    /** @var string Current class name for model resolution */
    public static $_class;

    /** @var array Static cache for query results */
    public static $_staticCache = [];

    /** @var array List templates for different views */
    protected static $_listTemplate = [
        Database::LIST_TEMPLATE_BASE => []
    ];

    /** @var bool Whether to return Model objects or arrays */
    protected static $_modelResponse = true;

    /** @var bool Whether to ignore deleted_at filtering */
    protected static $_ignoreExtend = false;

    /** @var bool Whether to ignore project_id filtering */
    protected static $_ignoreProject = false;

    /** @var string Alias for COUNT field in results */
    public static $_countField = 'cnt';

    /** @var bool Whether to filter by status (true = active only) */
    public static $_statusUse = true;

    /** @var string Status field name */
    public static $_statusField = '';

    /** @var array|int Status IDs to include */
    public static $_statusIds = [];

    /** @var bool Whether this table supports translation */
    public static $_translate = false;

    /** @var string Prefix for translation tables */
    public static $_transPrefix = 'T';

    /** @var string Foreign key in translation table linking to parent */
    public static $_transParent = 'parent';

    /** @var string Language ID field in translation table */
    public static $_transLanguageId = 'language_id';

    /** @var string Field name for aggregated translation block */
    public static $_transBlock = 'tblock';

    /** @var bool|array Language list for translations */
    public static $_transLanguageList = false;

    /** @var string Name field for getByName() lookups */
    public static $_name = null;

    /** @var bool Whether this table uses project isolation */
    public static $_useProject = true;

    /** @var bool Whether to use base query filtering */
    public static $_baseQuery = false;

    /** @var string Project ID field name */
    const PROJECT_ID = 'project_id';

    /** @var bool Whether to show queries in debug mode */
    public static $_showQuery = false;

    // ============================================================================
    // FIELD TYPE CONSTANTS
    // ============================================================================

    /** Integer type */
    const TYPE_INT = 0;

    /** Float/decimal type */
    const TYPE_FLOAT = 1;

    /** String/VARCHAR type */
    const TYPE_STRING = 2;

    /** Date type (without time) */
    const TYPE_DATE = 3;

    /** DateTime type (with time) */
    const TYPE_DATETIME = 4;

    /** TEXT type (long string) */
    const TYPE_TEXT = 5;

    /** JSON type (for structured data) */
    const TYPE_JSON = 6;

    /** Boolean type */
    const TYPE_BOOLEAN = 7;

    /**
     * Get string-based field types.
     * These types require quoting in queries.
     *
     * @return array
     */
    public static function getStringTypes()
    {
        return [
            self::TYPE_STRING,
            self::TYPE_TEXT,
            self::TYPE_DATE,
            self::TYPE_DATETIME,
            self::TYPE_JSON,
        ];
    }

    // ============================================================================
    // FIELD PARAMETER CONSTANTS
    // ============================================================================

    /** Field type */
    const FP_TYPE = 'type';

    /** Whether field is indexed */
    const FP_INDEX = 'index';

    /** Whether field allows NULL */
    const FP_NULL = 'null';

    /** Field length (for strings) */
    const FP_LENGTH = 'length';

    /** Whether to trim whitespace */
    const FP_TRIM = 'trim';

    /** Default value */
    const FP_DEFAULT = 'default';

    /** Cache flag */
    const FP_CACHE = 'cache';

    /** Link to another table/class */
    const FP_LINK = 'link';

    /**
     * Get the model name prefix.
     * Override for custom naming conventions.
     *
     * @return string
     */
    public static function getNamePrefix()
    {
        return 'Object';
    }

    /**
     * Get field definitions for this table.
     *
     * @return array
     */
    public static function getFields()
    {
        return static::$_fields;
    }

    // ============================================================================
    // RESPONSE FILTER CONSTANTS
    // ============================================================================

    /** Return objects/models */
    const FILTER_RESPONSE_OBJECTS = 'objects';

    /** Return count only */
    const FILTER_RESPONSE_COUNT = 'count';

    /**
     * Get the database name for this table.
     *
     * @return string|null Database name
     */
    public static function getBase()
    {
        return C::get(is_null(static::$_base) ? C::get()->main_database : static::$_base)->db_name;
    }

    /**
     * Set the database name.
     *
     * @param string $base Database configuration key
     *
     * @return string Database name
     */
    public static function setBase($base)
    {
        return C::get($base)['db_name'];
    }

    // ============================================================================
    // INVERSE CONDITION MAPPING
    // ============================================================================

    /**
     * Mapping of conditions to their inverse counterparts.
     * Used for generating NOT queries.
     *
     * @var array
     */
    public static $_inverseConditions = [
        Where::MORE => Where::LESS_EQUAL,
        Where::LESS => Where::MORE_EQUAL,
        Where::MORE_EQUAL => Where::LESS,
        Where::LESS_EQUAL => Where::MORE,
        Where::EQUAL => Where::NOT_EQUAL,
        Where::NOT_EQUAL => Where::EQUAL,
        Where::IS_NULL => Where::NOT_NULL,
        Where::NOT_NULL => Where::IS_NULL,
        Where::LIKE => Where::NOT_LIKE,
        Where::LIKE_START => Where::NOT_LIKE_START,
        Where::LIKE_END => Where::NOT_LIKE_END,
        Where::LIKE_EQUAL => Where::NOT_LIKE_EQUAL,
        Where::NOT_LIKE => Where::LIKE,
        Where::NOT_LIKE_START => Where::LIKE_START,
        Where::NOT_LIKE_END => Where::LIKE_END,
        Where::NOT_LIKE_EQUAL => Where::LIKE_EQUAL
    ];

    // ============================================================================
    // SELECT OBJECT CREATION
    // ============================================================================

    /**
     * Create a SELECT query object for this table.
     *
     * Configures the Select object with:
     * - Table name and alias
     * - Database name
     * - Field definitions (for query optimization)
     * - Primary key
     * - Translation table JOIN (if enabled)
     *
     * @param int $type Query type (SELECT, COUNT, REPLACE, DELETE)
     * @param bool $ignoreTranslate Whether to skip translation JOIN
     * @param string|bool $class Override class name for model resolution
     *
     * @return \Application\Assistance\Select Configured Select object
     */
    public static function getSelect($type = selectObj::SELECT, $ignoreTranslate = false, $class = false)
    {
        $class = $class === false ? get_called_class() : $class;
        static::$_class = '';
        $table = static::getNamespaceClass();

        static::$_select = new selectObj($type, $class);
        static::$_select->_map = static::getFields();
        static::$_select->_from = static::$_table;
        static::$_select->_base = static::getBase();
        static::$_select->_primary = static::$_index;

        /**
         * Handle translation table JOIN if enabled.
         * Builds a GROUP_CONCAT of all translations for the current language.
         */
        if ($type === selectObj::SELECT && (false === $ignoreTranslate && true === static::$_translate)) {
            $tClass = preg_replace(
                "/(.*?)([^\\\\]{1,})$/",
                "$1" . static::$_transPrefix . "$2",
                get_called_class()
            );

            static::$_select->_where[] = self::createWhere(
                (new selectObj\JoinField())->setBase(static::getBase())->setTable(static::$_table)->setField(static::$_index),
                null,
                Where::NOT_NULL,
                [$tClass::getBase(), $tClass::$_table],
                static::createWhere((new selectObj\JoinField())->setBase($tClass::getBase())->setTable($tClass::$_table)->setField($tClass::$_transParent), (new selectObj\JoinField())->setBase(static::getBase())->setTable(static::$_table)->setField(static::$_index))
            );

            $need = dfArrayIntersect(
                dfArrayKeys(static::getFields()),
                dfArrayKeys($tClass::getFields())
            );

            $add = [];
            foreach ($need as $v) {
                $add[] =
                    (new selectObj\FieldHandler())
                        ->setReplace('\r', '')
                        ->setField(
                            (new selectObj\FieldHandler())
                                ->setReplace('\n', '<br>')
                                ->setField(
                                    (new selectObj\FieldHandler())
                                        ->setReplace('"', '\\"')
                                        ->setField(
                                            (new selectObj\FieldHandler())
                                                ->setIfNull('')
                                                ->setTable($tClass::_name())
                                                ->setField($v)
                            )
                        )
                    );
            }
            static::$_select->_translateFields =
                (new selectObj\FieldHandler())
                    ->setConcat([
                        '{',
                        (new selectObj\FieldHandler())
                            ->setGroupConcat()
                            ->setField([
                                (new selectObj\FieldHandler())
                                    ->setConcat([
                                        '"',
                                        (new selectObj\FieldHandler())->setTable($tClass::_name())->setField(static::$_transLanguageId),
                                        '",:{',
                                        $add,
                                        '"})),"}")'
                                ])
                            ]),
                        '}'
                ])->setAlias(static::$_transBlock);
            static::$_select->_group[] = static::_name() . '.' . static::$_index;
        }

        return static::$_select;
    }

    /**
     * Set fields that should be logged when changed.
     *
     * @return void
     */
    public static function setFieldsToLog()
    {
        self::$_fieldsToLog = [];
    }

    /**
     * Get the display name for a field (for logging).
     *
     * @param string $field Field name
     *
     * @return string Display name
     */
    public static function getFieldToLog($field)
    {
        return isset(self::$_fieldsToLog[$field]) ? self::$_fieldsToLog[$field] : $field;
    }

    /** @var int|string Current project ID for filtering */
    protected static $_usedProject = CURRENT_PROJECT;

    /**
     * Set the project ID to use for filtering.
     *
     * @param int|int[] $project Project ID or array of IDs
     */
    public static function sertUsedProject($project)
    {
        self::$_usedProject = $project;
    }

    // ============================================================================
    // QUERY EXECUTION
    // ============================================================================

    /**
     * Execute the SELECT query and process results.
     *
     * @return array|bool|int Query results, count, or false on failure
     */
    protected static function select()
    {
        self::$_class = '';
        /** @var \Application\Assistance\DbEngine\EngineInterface $class */
        $class = '\Application\Assistance\DbEngine\\' . C::get(C::get()->main_database)->db_engine;
        $result = $class::select(static::$_select);

        /**
         * Handle COUNT queries.
         */
        if (static::$_select->_type == selectObj::COUNT) {
            if (dfCount(static::$_select->_fields) == 0) {
                self::setModelResponse(true);
                return $result[0][static::$_countField];
            } else {
                static::setModelResponse(false);
            }
        }

        /**
         * Handle REPLACE/INSERT queries.
         */
        if (static::$_select->_type == selectObj::REPLACE) {
            self::setModelResponse(true);
            return $result;
        }

        return self::finalSelect($result);
    }

    /**
     * Process query results into Models or arrays.
     *
     * @param mixed $result Raw query result
     *
     * @return array|bool Processed results
     */
    public static function finalSelect($result)
    {
        if (true === SHOW_QUERIES || isset($_REQUEST[SHOW_QUERY_FLAG]) || true === \Application\Assistance\Database::$_showQuery) {
            echo D_EOL . '=================================' . D_EOL
                . 'Model flag: ' . (false === static::$_modelResponse ? 'FALSE' : (true === static::$_modelResponse ? 'TRUE' : '"' . static::$_modelResponse . '"'))
                . D_EOL . '=================================' . D_EOL;
        }

        /**
         * Return as array (not models).
         */
        if (false === static::$_modelResponse) {
            static::setModelResponse(true);
            if (is_bool($result) || empty($result)) {
                return [];
            }
            if (!isset($result[0][static::$_index])) {
                return $result;
            }

            /**
             * Index results by primary key.
             */
            $res = [];
            $cnt = dfCount($result);
            for ($i = 0; $i < $cnt; $i++) {
                $res[$result[$i][static::$_index]] = $result[$i];
            }
            return $res;
        }

        /**
         * Return as Model objects.
         */
        if (!empty($result)) {
            $model = '\\' . preg_replace(
                    "/" . \Application\Crud::DATABASE_FOLDER . "\\\/",
                    '',
                    static::$_class == '' ? get_called_class() : static::$_class
                );

            $response = [];
            if (is_array($result) && !empty($result)) {
                foreach ($result as $k => $v) {
                    if (!is_object($v)) {
                        $response[$v[static::$_index]] = new $model($v);
                    } else {
                        $response[$k] = $v;
                    }
                }
                return $response;
            }
            return true;
        }

        return false;
    }

    /** @var array Database connection pool */
    private static $_connects = [];

    /**
     * Get the database engine instance.
     *
     * @param string|null $base Database configuration key
     *
     * @return mixed Engine connection
     */
    public static function getEngine($base = null)
    {
        static::$_baseBlock = $base === null ? C::get()->main_database : $base;
        static::$_base = C::get(static::$_baseBlock)->db_name;

        /** @var \Application\Assistance\DbEngine\EngineInterface $class */
        $class = '\Application\Assistance\DbEngine\\' . C::get(static::$_baseBlock)->db_engine;

        if (!isset(self::$_connects[$base])) {
            self::$_connects[$base] = $class::connect(
                C::get(static::$_baseBlock)->db_name,
                C::get(static::$_baseBlock)->db_host,
                C::get(static::$_baseBlock)->db_port,
                C::get(static::$_baseBlock)->db_login,
                C::get(static::$_baseBlock)->db_password
            );
        }

        return self::$_connects[$base];
    }

    // ============================================================================
    // CONDITION HELPERS
    // ============================================================================

    /**
     * Create a WHERE condition object.
     *
     * @param string $field Field name
     * @param mixed $value Value to compare
     * @param int|null $condition Condition type (default: EQUAL)
     * @param string|null $table Table alias (default: current table)
     * @param string|null $foreign Foreign key for JOIN
     * @param bool $leftJoin Whether to use LEFT JOIN
     *
     * @return Where
     */
    public static function createWhere($field, $value, $condition = null, $table = null, $foreign = null, $leftJoin = true)
    {
        return new selectObj\Where(
            $field,
            $value,
            is_null($condition) ? select\Where::EQUAL : $condition,
            is_null($table) ? static::$_table : $table,
            $foreign,
            $leftJoin
        );
    }

    /**
     * Create a WHERE block for grouping conditions.
     *
     * @param bool $type True for AND, false for OR
     *
     * @return Select\Block
     */
    public static function createBlock($type = true)
    {
        return new selectObj\Block($type);
    }

    // ============================================================================
    // RESPONSE MODE MANAGEMENT
    // ============================================================================

    /**
     * Set whether to return Model objects or arrays.
     *
     * @param bool $value True for Model objects, false for arrays
     */
    public static function setModelResponse($value)
    {
        if (true === SHOW_QUERIES || isset($_REQUEST[SHOW_QUERY_FLAG]) || true === \Application\Assistance\Database::$_showQuery) {
            echo D_EOL . '=================================' . D_EOL
                . 'Set model response as ' . ($value === false ? 'FALSE' : ($value === true ? 'TRUE' : '"' . $value . '"'))
                . ': ' . showCaller(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS))
                . D_EOL . '=================================' . D_EOL;
        }
        static::$_modelResponse = $value;
    }

    /**
     * Get the current response mode.
     *
     * @return bool
     */
    public static function getModelResponse()
    {
        return static::$_modelResponse;
    }

    /**
     * Check if deleted_at filtering is ignored.
     *
     * @return bool
     */
    public static function getIgnoreExtend()
    {
        return static::$_ignoreExtend;
    }

    /**
     * Set whether to ignore deleted_at filtering.
     *
     * @param bool $value
     */
    public static function setIgnoreExtend($value)
    {
        static::$_ignoreExtend = $value;
    }

    /**
     * Check if project_id filtering is ignored.
     *
     * @return bool
     */
    public static function getIgnoreProject()
    {
        return static::$_ignoreProject;
    }

    /**
     * Set whether to ignore project_id filtering.
     *
     * @param bool $value
     */
    public static function setIgnoreProject($value)
    {
        static::$_ignoreProject = $value;
    }

    // ============================================================================
    // CACHE MANAGEMENT
    // ============================================================================

    /**
     * Get the cache key for this table.
     *
     * @return string
     */
    public static function getCacheKey()
    {
        return static::$_table;
    }

    /**
     * Get a value from the static cache.
     *
     * @param mixed $id Cache key (auto-generated if null)
     *
     * @return bool|mixed Cached value or false
     */
    public static function getStaticCache($id = null)
    {
        if (empty($id)) {
            if (!empty(static::$_select->_where)) {
                $id = [];
                foreach (static::$_select->_where as $cond) {
                    /** @var \Application\Assistance\Select\Where $cond */
                    $id[] = ($cond->_field instanceof selectObj\JoinField) ? $cond->_field->_base . '.' . $cond->_field->_table . '.' . $cond->_field->_field : (is_array($cond->_field) ? implode('.', $cond->_field) : static::$_table . '.' . $cond->_field)
                        . ':' . $cond->_condition
                        . ':' . (is_array($cond->_value) ? implode(',', $cond->_value) : $cond->_value);
                }
            }
        }

        if (is_array($id)) {
            $id = implode('|', $id);
        }

        if (is_null($id)) {
            return false;
        }

        $result = (
            isset(static::$_staticCache[static::$_table])
            && isset(static::$_staticCache[static::$_table][$id])
        ) ? static::$_staticCache[static::$_table][$id] : false;

        \Application\Helpers\DfDebug::writeLog(
            D_EOL . 'Object (get static cache) ' . (false !== $result ? '' : 'not ')
            . 'such into static cache (ID: ' . $id . ')' . D_EOL . '------' . D_EOL
        );

        return $result;
    }

    /**
     * Store a value in the static cache.
     *
     * @param mixed $value Value to cache
     * @param mixed $id Cache key (auto-generated if null)
     */
    public static function setStaticCache($value, $id = null)
    {
        if (empty($id)) {
            if (!empty(static::$_select->_where)) {
                $id = [];
                foreach (static::$_select->_where as $cond) {
                    /** @var \Application\Assistance\Select\Where $cond */
                    $id[] = (($cond->_field instanceof selectObj\JoinField) ? $cond->_field->_base . '.' . $cond->_field->_table . '.' . $cond->_field->_field : (is_array($cond->_field) ? implode('.', $cond->_field) : static::$_table . '.' . $cond->_field)) . ':' . $cond->_condition
                        . ':' . (is_array($cond->_value) ? implode(',', $cond->_value) : $cond->_value);
                }
            }
        }

        if (is_array($id)) {
            $id = implode('|', $id);
        }

        if (!isset(static::$_staticCache[static::$_table])) {
            static::$_staticCache[static::$_table] = [];
        }

        static::$_staticCache[static::$_table][$id] = $value;

        \Application\Helpers\DfDebug::writeLog(
            'Set static object table: "' . static::$_table . '"; ID: "' . $id . '")' . D_EOL
            . '------' . D_EOL
        );
    }

    /**
     * Delete a value from the static cache.
     *
     * @param mixed $id Cache key
     */
    public static function deleteStaticCache($id)
    {
        if (!is_array($id) && (isset(static::$_staticCache[static::$_table]) && isset(static::$_staticCache[static::$_table][$id]))) {
            unset(static::$_staticCache[static::$_table][$id]);
        }
    }

    // ============================================================================
    // CRUD OPERATIONS
    // ============================================================================

    /**
     * Get one or more rows by primary key.
     *
     * @param int|array|null $ids Primary key(s) or null for all
     * @param bool|int $page Page number for pagination
     * @param bool|array $order Order by clause
     * @param bool $model Whether to return Model objects
     * @param bool $cache Whether to use cache
     *
     * @return array|bool|mixed Results
     */
    public static function getRow($ids = null, $page = false, $order = false, $model = true, $cache = true)
    {
        if (false === $ids) {
            return false;
        }

        if (false === $cache || !$result = static::getStaticCache($ids)) {
            static::setModelResponse($model);
            $select = static::getSelect();
            static::$_select->cache($cache);
            static::$_select->_page = $page;
            static::$_select->_order = $order;

            if (!is_null($ids)) {
                $select->_where[] = static::createWhere(static::$_index, $ids);
            }

            $result = static::select();

            if (!is_null($ids)) {
                static::setStaticCache($result, $ids);
            }
        }

        \Application\Helpers\DfDebug::writeLog(
            D_EOL . 'Object (get row) such into static cache (ids: '
            . (is_array($ids) ? implode(',', $ids) : $ids) . ')' . D_EOL . '------' . D_EOL
        );

        return is_bool($result) ? false : ((is_null($ids) || is_array($ids)) ? $result : array_pop($result));
    }

    /**
     * Get all rows with optional pagination and ordering.
     *
     * @param bool|int $page Page number
     * @param bool|array $order Order by clause
     * @param bool $model Whether to return Model objects
     *
     * @return array|bool Results
     */
    public static function getAll($page = false, $order = false, $model = true)
    {
        static::getSelect();
        static::setModelResponse($model);
        static::$_select->_page = $page;
        static::$_select->_order = $order;
        $result = static::select();
        return is_bool($result) ? false : $result;
    }

    /**
     * Get count of rows matching conditions.
     *
     * @param array $conditions WHERE conditions
     * @param bool|array $group Group by fields
     *
     * @return array|bool|int Count or grouped counts
     */
    public static function getCount($conditions = [], $group = false)
    {
        $select = static::getSelect(selectObj::COUNT);

        if (is_array($conditions) && dfCount($conditions) > 0) {
            foreach ($conditions as $field => $cond) {
                if (is_array($cond) && array_key_exists('value', $cond)) {
                    $select->_where[] = static::createWhere($field, $cond['value'], $cond['condition']);
                } else {
                    $select->_where[] = static::createWhere($field, $cond);
                }
            }
        }

        if (false !== $group) {
            /** @var array $group */
            $fieldsGroup = [];
            foreach ($group as $k => $v) {
                $fieldsGroup[] = preg_match("/^[A-Z]{1}/", $v)
                    ? (new selectObj\FieldHandler())->setField($v)->setAlias('df' . $k) :
                    (new selectObj\FieldHandler())->setField($v)->setTable(static::$_table)->setBase(self::getBase());
            }
            $fieldsGroup[] = (new selectObj\FieldHandler())->setCount()->setAlias(\Application\Assistance\Database::$_countField);
            $select->setFields($fieldsGroup)->setGroups($group);
        }

        return static::select();
    }

    /**
     * Get rows by the current conditions.
     *
     * @param bool|int $page Page number
     * @param bool|array $order Order by clause
     * @param bool $cache Whether to use cache
     * @param bool|int $offset Row limit
     * @param bool $pop Whether to return a single row
     *
     * @return array|bool Results
     */
    public static function getByCondition($page = false, $order = false, $cache = true, $offset = false, $pop = false)
    {
        static::$_select->_page = $page;
        static::$_select->_order = $order;
        static::$_select->_offset = $offset;

        $result = true === $cache ? self::getStaticCache() : false;

        if (false === $result) {
            $result = self::select();
            if (true === $cache) {
                self::setStaticCache($result, null);
            }
        }

        return is_bool($result) ? false : (false === $pop ? $result : array_pop($result));
    }

    /**
     * Get list template configuration.
     *
     * @param bool|string $template Template name
     *
     * @return array|mixed
     */
    public static function getListTemplate($template = false)
    {
        return false === $template ? static::$_listTemplate : static::$_listTemplate[$template];
    }

    /**
     * Save (insert or update) a record.
     *
     * @param array $params Field values
     * @param int $projectId Project ID
     *
     * @return array|bool Save result
     */
    public static function save($params, $projectId = CURRENT_PROJECT)
    {
        static::$_staticCache[static::$_table] = [];
        $update = self::getSelect(selectObj::REPLACE);
        $update->_fields = $params;
        $result = self::select();
        return $result;
    }

    /**
     * Remove records matching conditions.
     *
     * @param array $params WHERE conditions (field => value)
     *
     * @return array|bool Deletion result
     */
    public static function remove($params)
    {
        static::$_staticCache[static::$_table] = [];
        $select = static::getSelect(selectObj::DELETE);

        foreach ($params as $k => $v) {
            $select->_where[] = static::createWhere($k, $v);
        }

        return static::select();
    }

    /**
     * Get translations for a record.
     *
     * @param int $id Parent record ID
     *
     * @return array|bool Translations
     */
    public static function getTranslate($id)
    {
        $select = self::getSelect();
        $select->_where[] = self::createWhere(self::$_transParent, $id);
        return self::getByCondition();
    }

    /**
     * Get the fully qualified namespace for this class.
     *
     * @return string
     */
    public static function getNamespaceClass()
    {
        return '\\' . static::class;
    }

    /**
     * Get the qualified table name (database.table).
     *
     * @return string
     */
    public static function _name()
    {
        return static::getBase() . '.' . static::$_table;
    }

    /**
     * Find a record by its name field.
     *
     * @param string $search Name to search for
     *
     * @return array|bool Matching records
     */
    public static function getByName($search)
    {
        $search = dfTrim($search);
        return (null === static::$_name || empty($search))
            ? false
            : (static::getSelect())
                ->addWhere(static::createWhere(static::$_name, dfTrim($search)))
                ->order(static::$_name)
                ->result();
    }
}