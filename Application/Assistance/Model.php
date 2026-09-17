<?php

/**
 * Base ORM model class.
 *
 * This is the core model class that provides Object-Relational Mapping
 * between database tables and PHP objects. It handles:
 *
 * - Automatic getter/setter generation from database fields
 * - Type conversion and validation (INT, FLOAT, STRING, DATE, JSON, BOOLEAN)
 * - Relationship management (one-to-one, one-to-many, many-to-many)
 * - Translation/localization support
 * - JSON data field handling (protected data)
 * - Magic method calls for dynamic properties
 * - Save/update operations with automatic type casting
 * - Static and dynamic cache integration
 *
 * The Model class is extended by each table's model class (e.g.,
 * \Modules\Base\Models\Person). The corresponding DbTable class
 * (e.g., \Modules\Base\Models\DbTables\Person) defines the table schema.
 *
 * @package   Application\Assistance
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */

namespace Application\Assistance;

use \Application\Helpers\Database as hdb;
use \Application\Assistance\Database as db;
use \Application\Helpers\Date as D;

abstract class Model
{
    use \Application\Assistance\Controller\TraitClass;

    /** @var array Flip mapping of field names to methods */
    protected $_methodListFlip;

    /** @var array Method mapping for getters/setters/links */
    protected $_methods;

    /** @var array Current field values */
    public $_fields = [];

    /** @var array|null Package data for batch operations */
    protected $_package = null;

    /** @var array Default field type template */
    protected $_typeTemplate = [
        db::FP_TYPE => db::TYPE_INT,
        db::FP_NULL => false,
        db::FP_TRIM => true,
        db::FP_DEFAULT => 0,
        db::FP_INDEX => false,
        db::FP_CACHE => false,
        db::FP_LENGTH => null,
    ];

    /** @var array Method list for all fields */
    public $_methodList;

    /** @var \Application\Assistance\Database Database table class */
    public $_db;

    /** @var bool|array Translation block data */
    public $_translateBlock = false;

    /** @var mixed Updated at timestamp */
    public $updated_at;

    /** @var mixed Deleted at timestamp */
    public $deleted_at;

    /**
     * Initialize a new model instance.
     *
     * @param array $data    Initial field values
     * @param array|null $package Package data for batch operations
     */
    public function __construct($data = [], $package = null)
    {
        $this->startPackage($package);

        /**
         * Resolve the DbTable class from the Model class name.
         */
        /** @var \Application\Assistance\Database $db */
        $db = preg_replace(
            '/' . \Application\Crud::MODEL_FOLDER . '/',
            \Application\Crud::MODEL_FOLDER . "\\" . \Application\Crud::DATABASE_FOLDER,
            get_called_class()
        );
        $this->_db = $db;

        $fields = $db::getFields();
        if (empty($fields)) {
            new Exception('Method description not found', false);
        }

        /**
         * Parse translation block if present.
         */
        if (isset($data[db::$_transBlock])) {
            $data[db::$_transBlock] = preg_replace([
                "/[[:cntrl:]]/",
                "/\//"
            ], [
                '',
                '\\\/'
            ], $data[db::$_transBlock]);

            if (!($this->_translateBlock = dfJsonDecode($data[db::$_transBlock], true))) {
                $this->_translateBlock = [];
            }
        }

        /**
         * Build getter/setter method mappings.
         */
        $this->_methodList = \Application\Helpers\Line::getMethods(dfArrayKeys($fields));
        $this->_methodListFlip = array_flip($this->_methodList);

        foreach ($this->_methodList as $field => $method) {
            $this->_methods[hdb::PREFIX_GET . $method] = [false, $field];
            $this->_methods[hdb::PREFIX_SET . $method] = [true, $field];

            if (isset($fields[$field][Database::FP_LINK])) {
                $this->_methods[hdb::PREFIX_LINK . $method] = [
                    $fields[$field][Database::FP_LINK],
                    $field
                ];
            }

            $this->$field = isset($data[$field]) ? $data[$field] : (null !== $this->_package ? $this->_package[0] : '');

            if (isset($fields[$field]) && is_array($fields[$field])) {
                $this->_fields[$field] = dfArrayMerge($this->_typeTemplate, $fields[$field]);
            }
        }
    }

    /**
     * Magic method for dynamic getters/setters/links.
     *
     * Handles:
     * - getField(): Returns field value (with translation if available)
     * - setField($value): Sets field value with type validation
     * - linkField(): Returns related model for foreign keys
     * - lotsFieldInModuleByField(): Returns one-to-many relationships
     * - relatedFieldInModuleByField(): Returns reverse relationships
     *
     * @param string $method Method name
     * @param mixed  $value  Method arguments
     *
     * @return mixed|$this|Exception
     */
    public function __call($method, $value = null)
    {
        if (isset($this->_methods[$method])) {
            /**
             * SET method: setter
             */
            if (true === $this->_methods[$method][0]) {
                $field = $this->_methods[$method][1];
                $value = $this->baseModelcheck($this->_fields[$field], $method, $value[0]);
                $this->$field = $value;
                return $this;
            }
            /**
             * GET method: getter with translation support
             */
            else if (false === $this->_methods[$method][0]) {
                // Determine language for translation
                if (null !== $value && is_array($value) && dfCount($value) > 0) {
                    $lang = $value[0];
                } else if (defined('DEFAULT_LANGUAGE')) {
                    $lang = \Application\Translate::getLanguage();
                } else if (isset($_COOKIE[Controller\Controller::FREE_LANGUAGE_COOKIE])) {
                    $lang = $_COOKIE[Controller\Controller::FREE_LANGUAGE_COOKIE];
                } else {
                    $lang = null;
                }

                // Return translated value if available
                if (!is_null($lang)) {
                    if (\Application\Assistance\Database::$_transLanguageId !== $this->_methods[$method][1]
                        && isset($this->_translateBlock[$lang])
                        && isset($this->_translateBlock[$lang][$this->_methods[$method][1]])
                        && dfTrim($this->_translateBlock[$lang][$this->_methods[$method][1]]) !== '') {
                        $direction = defined('TEXT_DIRECTION') && TEXT_DIRECTION > 0
                            ? ['<rtl dir="rtl">', '</rtl>']
                            : ['', ''];
                        return $direction[0] . $this->_translateBlock[$lang][$this->_methods[$method][1]] . $direction[1];
                    }
                }
                return $this->{$this->_methods[$method][1]};
            }
            /**
             * LINK method: one-to-one relationship
             */
            else {
                $value = $this->{$this->_methods[$method][1]};
                if ($value > 0) {
                    $class = $this->_methods[$method][0];
                    /** @var Database $class */
                    return $class::getRow($value);
                }
                return false;
            }
        } else {
            /**
             * Dynamic relationship methods: lots/related
             * Format: lotsFieldInModuleByField or relatedFieldInModuleByField
             */
            if (preg_match("/^(" . hDb::PREFIX_LOTS . "|" . hDb::PREFIX_RELATED . ")(.*?)In(.*)By(.*?)$/", $method, $m)) {
                $class = '\\' . \Application\Crud::MODULE_FOLDER
                    . '\\' . $m[3]
                    . '\\' . \Application\Crud::MODEL_FOLDER
                    . '\\' . \Application\Crud::DATABASE_FOLDER
                    . '\\' . $m[2];
                /** @var Database $class */
                return hDb::getDinamicly(
                    $class,
                    $this->_methodListFlip[$m[4]],
                    $this->{hdb::PREFIX_GET . $m[4]}(),
                    $m[1] === hDb::PREFIX_RELATED
                );
            }
        }

        return new Exception($method . ': ' . 'unknown method in class' . ' ' . get_called_class(), false);
    }

    /** @var string|null JSON data field name for protected data */
    protected $_dataField = null;

    /**
     * Set protected/JSON data fields.
     *
     * Stores arbitrary data in a JSON field within the model.
     *
     * @param bool|string $field Field key to set
     * @param bool|mixed  $value Value to store
     * @param array       $arr   Bulk data to set
     *
     * @return $this
     */
    public function setProtected($field = false, $value = false, $arr = [])
    {
        if (null !== $this->_dataField) {
            $this->setPrivate();

            if (dfCount($arr) > 0) {
                foreach ($arr as $k => $v) {
                    $this->_data[$k] = $v;
                }
            } else {
                $this->_data[$field] = $value;
            }

            $this->{\Application\Helpers\Database::PREFIX_SET . $this->_dataField}(dfJsonEncode($this->_data));
        }
        return $this;
    }

    /**
     * Initialize protected data from JSON field.
     */
    protected function setPrivate()
    {
        if (null !== $this->_dataField && false === $this->_data) {
            $res = $this->{\Application\Helpers\Database::PREFIX_GET . $this->_dataField}();
            if (empty($res)) {
                $this->_data = [];
            }
            if ($res = dfJsonDecode($res, true)) {
                $this->_data = $res;
            } else {
                $this->_data = [];
            }
        }
    }

    /**
     * Initialize package data for batch operations.
     *
     * @param array|null $arr Package configuration
     *
     * @return $this
     */
    public function startPackage($arr)
    {
        $this->_package = null === $arr
            ? null
            : [
                array_fill_keys($arr, null),
                array_fill_keys($arr, false)
            ];
        return $this;
    }

    /**
     * Validate and cast field values based on field type.
     *
     * @param array  $rule   Field validation rules
     * @param string $method Method name (for error reporting)
     * @param mixed  $value  Input value
     *
     * @return array|mixed Validated and cast value
     */
    protected function baseModelcheck($rule, $method, $value)
    {
        $arr = false;
        if (!is_array($value)) {
            $value = [$value];
            $arr = true;
        }

        foreach ($value as &$v) {
            if (false === $rule[db::FP_NULL] && empty($v)) {
                new Exception($method . ': ' . 'method in model does not support null value' . ' ' . get_called_class(), false);
            }

            switch ($rule[db::FP_TYPE]) {
                case db::TYPE_INT:
                    $v = (int)$v;
                    break;
                case db::TYPE_FLOAT:
                    $v = (float)$v;
                    break;
                case db::TYPE_STRING:
                case db::TYPE_TEXT:
                    $v = true === $rule[db::FP_TRIM] ? dfTrim($v) : (string)$v;
                    break;
                case db::TYPE_DATE:
                    $v = D::dbDate($v);
                    break;
                case db::TYPE_DATETIME:
                    $v = D::dbDateTime($v);
                    break;
                case db::TYPE_BOOLEAN:
                    $v = hdb::checkBoolean($v);
                    break;
                case db::TYPE_JSON:
                    if (is_array($v)) {
                        $v = dfJsonEncode($v);
                    } else {
                        if (!dfJsonDecode($v)) {
                            $v = '[]';
                        }
                    }
                    break;
                default:
                    new Exception('Unknown data type' . ' ' . $rule[db::FP_TYPE], false);
                    break;
            }
        }

        return true === $arr ? array_pop($value) : $value;
    }

    /**
     * Bulk set field values from array.
     *
     * @param array $data Field values
     *
     * @return $this
     */
    public function baseModelset($data)
    {
        foreach ($this->_methodList as $field => $method) {
            $this->{hdb::PREFIX_SET . $method}(isset($data[$field]) ? $data[$field] : null);
        }
        return $this;
    }

    /**
     * Convert model to array.
     *
     * @return array All field values with proper types
     */
    public function toArray()
    {
        $response = [];
        $res = null;

        foreach ($this->_fields as $field => $data) {
            $res = $this->{$field};
            if (!is_array($res)) {
                $res = [$res];
            }

            foreach ($res as &$v) {
                switch ($data[db::FP_TYPE]) {
                    case db::TYPE_INT:
                    case db::TYPE_FLOAT:
                        if (true === $data[db::FP_NULL] && empty($v)) {
                            $v = null;
                        } else {
                            $v = $data[db::FP_TYPE] == db::TYPE_INT ? (int)$v : (float)$v;
                        }
                        break;
                    case db::TYPE_BOOLEAN:
                        if (true === $data[db::FP_NULL] && empty($v)) {
                            $v = false;
                        } else {
                            $v = $data[db::FP_TYPE] == !!$v;
                        }
                        break;
                    case db::TYPE_STRING:
                    case db::TYPE_TEXT:
                    case db::TYPE_JSON:
                        if (true === $data[db::FP_NULL] && empty($v)) {
                            $v = null;
                        } else {
                            $v = (string)$v;
                        }
                        break;
                    case db::TYPE_DATE:
                    case db::TYPE_DATETIME:
                        if (true === $data[db::FP_NULL] && empty($v)) {
                            $v = null;
                        } else {
                            $v = $data[db::FP_TYPE] == db::TYPE_DATE ? D::dbDate($v) : D::dbDateTime($v);
                        }
                        break;
                }
            }

            $response[$field] = null === $this->_package ? array_pop($res) : $res;
        }

        return $response;
    }

    /**
     * Save the model to the database.
     *
     * @param int $projectId Project ID
     *
     * @return array|bool Save result
     */
    public function save($projectId = CURRENT_PROJECT)
    {
        $data = is_object($this) ? $this->toArray() : $this;
        $db = $this->_db;
        $id = $db::save($data, $projectId);

        if (null === $this->_package) {
            $this->{\Application\Helpers\Database::PREFIX_SET . $this->_methodList[$db::$_index]}(is_array($id) ? array_pop($id) : $id);
        }

        $this->_package = null;
        return $id;
    }

    /**
     * Get translations for this record.
     *
     * @param bool|int $id Specific language ID
     *
     * @return array|\Application\Assistance\Model|false
     */
    public function getTranslate($id = false)
    {
        $db = preg_replace(
            "/([a-z]{1,})$/i",
            \Application\Crud::DATABASE_FOLDER . "\T$1",
            get_called_class()
        );
        $parentDb = $this->_db;
        /** @var \Application\Assistance\Database $db */
        /** @var \Application\Assistance\Database $parentDb */

        $index = \Application\Helpers\Line::getMethods($parentDb::$_index)[$parentDb::$_index];
        $result = $db::getTranslate($this->{hdb::PREFIX_GET . $index}());

        if (is_bool($result)) {
            return [];
        }

        $res = [];
        foreach ($result as $v) {
            /** @var \Modules\Geo\Models\TLanguage $v */
            $res[$v->getLanguageId()] = $v;
        }

        return false === $id ? $res : (isset($res[$id]) ? $res[$id] : false);
    }

    /** @var bool|array Cached JSON data */
    protected $_data = false;

    /**
     * Get protected data from JSON field.
     *
     * @param null|string|array $param   Specific key(s) to retrieve
     * @param mixed             $default Default value if key not found
     *
     * @return mixed Requested data
     */
    public function getData($param = null, $default = false)
    {
        $this->setPrivate();

        if (is_null($param)) {
            return $this->_data;
        }

        if (is_array($param)) {
            $res = [];
            foreach ($param as $v) {
                $res[$v] = !isset($this->_data[$v]) ? $default : $this->_data[$v];
            }
            return $res;
        }

        return !isset($this->_data[$param]) ? $default : $this->_data[$param];
    }

    /**
     * Get the primary key value.
     *
     * @return string|int
     */
    public function _id()
    {
        $parentDb = $this->_db;
        /** @var \Application\Assistance\Database $parentDb */
        return $this->{hdb::PREFIX_GET . \Application\Helpers\Line::getMethods($parentDb::$_index)[$parentDb::$_index]}();
    }

    /**
     * Get the name/display field value.
     *
     * @param mixed $default Default value if name field is empty
     *
     * @return string
     */
    public function _name($default = null)
    {
        $parentDb = $this->_db;
        /** @var \Application\Assistance\Database $parentDb */
        return $this->getMagicField($default, $parentDb::$_index, $parentDb::$_name);
    }

    /**
     * Get a field value with fallback.
     *
     * @param mixed      $default Default value
     * @param string     $index   Primary key field
     * @param string|null $field  Name field
     *
     * @return string
     */
    protected function getMagicField($default, $index, $field)
    {
        $parentDb = $this->_db;
        /** @var \Application\Assistance\Database $parentDb */

        $index = \Application\Helpers\Line::getMethods($index)[$index];
        $result = $this->{hdb::PREFIX_GET . (null === $field ? $index : \Application\Helpers\Line::getMethods($field)[$field])}();

        $id = $this->{hdb::PREFIX_GET . $index}();
        $prefix = empty($result) ? ($default === null ? $parentDb::getNamePrefix() . ' #' : $default) . $id : '';

        if (!empty($result)) {
            $prefix = (defined('TEXT_DIRECTION') && TEXT_DIRECTION > 0 ? '<rtl dir="rtl">' : '') . $result . (defined('TEXT_DIRECTION') && TEXT_DIRECTION > 0 ? '</rtl>' : '');
        }

        return $prefix;
    }

    /**
     * Set a field value using magic setter.
     *
     * @param string $field Field name
     * @param mixed  $value Value to set
     *
     * @return $this
     */
    protected function setMagicField($field, $value)
    {
        $this->{hdb::PREFIX_SET . \Application\Helpers\Line::getMethods($field)[$field]}($value);
        return $this;
    }
}