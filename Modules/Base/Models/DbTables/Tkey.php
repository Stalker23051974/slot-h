<?php

namespace Modules\Base\Models\DbTables;

use Application\Assistance\Select\FieldHandler;
use Application\Assistance\Select\JoinField;
use \Modules\Base\Models\DbTables\Tvalue as dBTvalue;
use \Application\Assistance\Select\Where;

/**
 * Tkey database table class.
 *
 * Manages translation keys for the localization system. Each unique
 * text string in the code has a corresponding key entry with an
 * MD5 hash for efficient lookup.
 *
 * Features:
 * - Text key storage with MD5 hashing
 * - Translation value association via Tvalue table
 * - Language-specific value retrieval
 * - Filtering and pagination support
 * - Used by the translation system for code localization
 *
 * @package   Modules\Base\Models\DbTables
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method static \Modules\Base\Models\Tkey|array|false getRow($ids = null, $page = false, $order = false, $model = true, $cache = true)
 * @method static \Modules\Base\Models\Tkey[]|array|false getAll($page = false, $order = false, $model = true)
 */
class Tkey extends \Application\Assistance\DatabaseNormal
{
    /** Primary key field */
    const TKEY_ID = 'tkey_id';

    /** Original text string */
    const TKEY_KEY = 'tkey_key';

    /** MD5 hash of the text string */
    const TKEY_HASH = 'tkey_hash';

    /** Table name */
    public static $_table = 'tkeys';

    /** Primary key field */
    public static $_index = self::TKEY_ID;

    /**
     * Field definitions.
     *
     * @var array
     */
    public static $_fields = [
        self::TKEY_ID => [],
        self::TKEY_KEY => [self::FP_TYPE => self::TYPE_TEXT, self::FP_INDEX => true],
        self::TKEY_HASH => [self::FP_TYPE => self::TYPE_STRING],
    ];

    /** Filter by name/text */
    const FILTER_NAME = '__name';

    /** Filter by language */
    const FILTER_LANGUAGE = '__language';

    /** Filter by value */
    const FILTER_VALUE = '__value';

    /**
     * Get translation values by language and optional hash.
     *
     * @param int|string $language Language ID
     * @param bool|string $hash    Optional hash filter
     *
     * @return \Modules\Base\Models\Tkey[]|false
     */
    public static function getter($language, $hash)
    {
        return (static::getSelect())
            ->setFields([(new FieldHandler())->setTable(dBTvalue::_name())->setField(dBTvalue::TVALUE_VALUE)])
            ->addWhere([
                self::createWhere(
                    (new JoinField())->setBase(dBTvalue::getBase())->setTable(dBTvalue::$_table)->setField(dBTvalue::LANGUAGE_ID),
                    $language,
                    \Application\Assistance\Select\Where::EQUAL,
                    [dBTvalue::getBase(), dBTvalue::$_table],
                    [
                        static::createWhere((new JoinField())->setBase(dBTvalue::getBase())->setTable(dBTvalue::$_table)->setField(dBTvalue::TKEY_ID), (new JoinField())->setBase(self::getBase())->setTable(self::$_table)->setField(self::TKEY_ID))
                    ]
                ),
                static::createWhere(self::TKEY_HASH, $hash)
            ])
            ->result();
    }

    /**
     * Get a translation key by hash.
     *
     * @param string $hash MD5 hash of the text
     *
     * @return \Modules\Base\Models\Tkey|false
     */
    public static function getByHash($hash = false)
    {
        return (static::getSelect())
            ->addWhere(static::createWhere(self::TKEY_HASH, $hash))
            ->pop()
            ->result();
    }

    /**
     * Get translated value by hash for current language.
     *
     * @param string $hash MD5 hash of the text
     *
     * @return string|bool Translated text or false if not found
     */
    public static function get($hash)
    {
        self::setModelResponse(false);
        $result = self::getter(\Application\Translate::getLanguage(), $hash);
        return is_bool($result) ? false : $result[0][dBTvalue::TVALUE_VALUE];
    }

    /**
     * Get translation keys with filters and pagination.
     *
     * @param int   $page    Page number
     * @param array $filters Filter criteria
     * @param bool  $tree    Reserved for future use
     *
     * @return object Object with objects and count properties
     */
    public static function getByFilters($page, $filters, $tree = false)
    {
        $select = self::getSelect();

        if (isset($filters['__total'])) {
            $select->addWhere(self::createWhere(self::TKEY_KEY, dfTrim($filters['__total']), \Application\Assistance\Select\Where::LIKE));
        } else {
            foreach ([
                         self::FILTER_NAME,
                         self::FILTER_LANGUAGE,
                         self::FILTER_VALUE,
                     ] as $field) {
                if (isset($filters[$field]) && ((is_array($filters[$field]) && dfCount($filters[$field]) > 0) || dfTrim($filters[$field]) != '')) {
                    switch ($field) {
                        case self::FILTER_NAME:
                            $select->addWhere(self::createWhere(self::TKEY_KEY, dfTrim($filters[$field]), Where::LIKE));
                            break;
                        case self::FILTER_LANGUAGE:
                            $select->addWhere(self::createWhere(
                                (new JoinField())->setBase(self::getBase())->setTable(self::$_table)->setField(self::TKEY_ID),
                                null,
                                Where::NOT_NULL,
                                [Tvalue::getBase(), Tvalue::$_table],
                                [
                                    static::createWhere((new JoinField())->setBase(dBTvalue::getBase())->setTable(dBTvalue::$_table)->setField(dBTvalue::TKEY_ID), (new JoinField())->setBase(self::getBase())->setTable(self::$_table)->setField(self::TKEY_ID)),
                                    static::createWhere((new JoinField())->setBase(dBTvalue::getBase())->setTable(dBTvalue::$_table)->setField(dBTvalue::LANGUAGE_ID), $filters['__total'])
                                ]
                            ));
                            break;
                        case self::FILTER_VALUE:
                            $select->addWhere(self::createWhere(
                                (new JoinField())->setBase(self::getBase())->setTable(self::$_table)->setField(self::TKEY_ID),
                                null,
                                Where::NOT_NULL,
                                [Tvalue::getBase(), Tvalue::$_table],
                                [
                                    static::createWhere((new JoinField())->setBase(dBTvalue::getBase())->setTable(dBTvalue::$_table)->setField(dBTvalue::TKEY_ID), (new JoinField())->setBase(self::getBase())->setTable(self::$_table)->setField(self::TKEY_ID)),
                                    static::createWhere((new JoinField())->setBase(dBTvalue::getBase())->setTable(dBTvalue::$_table)->setField(dBTvalue::TVALUE_VALUE), dfTrim($filters['__total']), Where::LIKE)
                                ]
                            ));
                            break;
                    }
                }
            }
        }

        $select->_page = $page;
        $where = $select->_where;

        if (!$res = self::getByCondition($page)) {
            return (object)[
                self::FILTER_RESPONSE_OBJECTS => false,
                self::FILTER_RESPONSE_COUNT => 0
            ];
        }

        return (object)[
            self::FILTER_RESPONSE_OBJECTS => $res,
            self::FILTER_RESPONSE_COUNT => (self::getSelect(\Application\Assistance\Select::COUNT))
                ->addWhere($where)
                ->result()
        ];
    }
}