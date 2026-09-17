<?php

namespace Modules\Base\Models\DbTables;

use \Application\Assistance\Controller\Controller as CC;
use Application\Assistance\Select\JoinField;
use Application\Assistance\Select\Where;
use \Config\CC as C;
use \Modules\Base\Models\DbTables as db;

/**
 * PersonPrivate database table class.
 *
 * Manages user authentication credentials and private data.
 * This table stores login information, passwords, and authentication
 * status for users.
 *
 * Features:
 * - Login and nickname uniqueness
 * - Password hashing (MD5, SHA1, Argon2 support)
 * - Ban status tracking
 * - Session management
 * - Authentication verification
 * - UID-based session tracking
 *
 * @package   Modules\Base\Models\DbTables
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 *
 * @method static \Modules\Base\Models\PersonPrivate|array|false getRow($ids = null, $page = false, $order = false, $model = true, $cache = true)
 * @method static \Modules\Base\Models\PersonPrivate[]|array|false getAll($page = false, $order = false, $model = true)
 */
class PersonPrivate extends \Application\Assistance\DatabaseNormal
{
    /** Primary key field */
    const PERSON_PRIVATE_ID = 'person_private_id';

    /** Person ID (FK to Person table) */
    const PERSON_ID = 'person_id';

    /** Banned status */
    const PERSON_PRIVATE_BANNED = 'person_private_banned';

    /** Login username */
    const PERSON_PRIVATE_LOGIN = 'person_private_login';

    /** Nickname (display name) */
    const PERSON_PRIVATE_NICKNAME = 'person_private_nickname';

    /** Password hash */
    const PERSON_PRIVATE_HASH = 'person_private_hash';

    /** Unique identifier for session tracking */
    const PERSON_PRIVATE_UID = 'person_private_uid';

    /** Project ID (FK to Project table) */
    const PROJECT_ID = 'project_id';

    /** Table name */
    public static $_table = 'person_privates';

    /** Primary key field */
    public static $_index = self::PERSON_PRIVATE_ID;

    /**
     * Field definitions.
     *
     * @var array
     */
    public static $_fields = [
        self::PERSON_PRIVATE_ID => [],
        self::PERSON_ID => [
            self::FP_INDEX => true,
            self::FP_LINK => Person::class
        ],
        self::PERSON_PRIVATE_BANNED => [
            self::FP_TYPE => self::TYPE_BOOLEAN,
            self::FP_INDEX => true
        ],
        self::PERSON_PRIVATE_LOGIN => [
            self::FP_TYPE => self::TYPE_STRING,
            self::FP_INDEX => true,
            self::FP_NULL => true
        ],
        self::PERSON_PRIVATE_NICKNAME => [
            self::FP_TYPE => self::TYPE_STRING,
            self::FP_INDEX => true,
            self::FP_NULL => true
        ],
        self::PERSON_PRIVATE_HASH => [
            self::FP_TYPE => self::TYPE_STRING,
            self::FP_INDEX => true,
            self::FP_NULL => true
        ],
        self::PERSON_PRIVATE_UID => [
            self::FP_TYPE => self::TYPE_STRING,
            self::FP_INDEX => true,
            self::FP_NULL => true
        ],
        self::PROJECT_ID => [
            self::FP_LINK => Project::class
        ],
    ];

    /**
     * Get user by login with status and project filtering.
     *
     * @param string $login Login username
     * @param int $projectId Project ID
     *
     * @return \Modules\Base\Models\PersonPrivate|false
     */
    public static function getByLogin($login, $projectId = CURRENT_PROJECT)
    {
        return (self::getSelect())->addWhere([
            self::createWhere(self::PERSON_PRIVATE_LOGIN, $login),
            self::createWhere(
                (new JoinField())->setBase(Person::getBase())->setTable(Person::$_table)->setField(Person::PERSON_ID),
                null,
                Where::NOT_NULL,
                [Person::getBase(), Person::$_table],
                [
                    static::createWhere((new JoinField())->setBase(Person::getBase())->setTable(Person::$_table)->setField(Person::PERSON_ID), (new JoinField())->setBase(self::getBase())->setTable(self::$_table)->setField(self::PERSON_ID)),
                    static::createWhere((new JoinField())->setBase(Person::getBase())->setTable(Person::$_table)->setField(Person::PERSON_ID), $projectId)
                ]
            )
        ])->pop()->result();
    }

    /**
     * Get user by nickname with status and project filtering.
     *
     * @param string $nick Nickname
     * @param int $projectId Project ID
     *
     * @return \Modules\Base\Models\PersonPrivate|false
     */
    public static function getByNickname($nick, $projectId = CURRENT_PROJECT)
    {
        return (self::getSelect())->addWhere([
            self::createWhere(self::PERSON_PRIVATE_NICKNAME, $nick),
            self::createWhere(
                (new JoinField())->setBase(self::getBase())->setTable(self::$_table)->setField(self::PERSON_ID),
                null,
                Where::NOT_NULL,
                [Person::getBase(), Person::$_table],
                [
                    static::createWhere((new JoinField())->setBase(Person::getBase())->setTable(Person::$_table)->setField(Person::PERSON_ID), (new JoinField())->setBase(self::getBase())->setTable(self::$_table)->setField(self::PERSON_ID)),
                    static::createWhere((new JoinField())->setBase(Person::getBase())->setTable(Person::$_table)->setField(Person::PERSON_ID), $projectId)
                ]
            )
        ])->pop()->result();
    }

    /**
     * Get user by UID (session identifier).
     *
     * @param string $label UID value
     * @param int $project Project ID
     *
     * @return \Modules\Base\Models\PersonPrivate|false
     */
    public static function getByUid($label, $project = CURRENT_PROJECT)
    {
        return (self::getSelect())->addWhere([
            self::createWhere(self::PERSON_PRIVATE_UID, $label),
            self::createWhere(self::PROJECT_ID, $project)
        ])->pop()->result();
    }

    /**
     * Get user by password hash.
     *
     * @param string $hash Password hash
     *
     * @return \Modules\Base\Models\PersonPrivate|false
     */
    public static function getByHash($hash)
    {
        return (self::getSelect())->addWhere([
            self::createWhere(self::PERSON_PRIVATE_HASH, $hash),
            self::createWhere(self::PROJECT_ID, CURRENT_PROJECT)
        ])->pop()->result();
    }

    /** Active user status */
    const PERSON_ACTIVE = 1;

    /** Banned user status */
    const PERSON_BANNED = 2;

    /**
     * Check current authentication via session/cookie.
     *
     * @param int $project Project ID
     *
     * @return false|\Modules\Base\Models\PersonPrivate
     */
    public static function checkAuth($project = CURRENT_PROJECT)
    {
        $hash = '';
        $projectId = CC::API_HASH_NAME . $project;

        if (!isset($_SESSION[$projectId]) || !isset($_COOKIE[$projectId]) || $_SESSION[$projectId] != $_COOKIE[$projectId]) {
            if (isset($_SESSION[$projectId])) {
                $hash = $_SESSION[$projectId];
                setcookie($projectId, $hash, isset($_SESSION[CC::SESSION_TTL_NAME]) ? $_SESSION[CC::SESSION_TTL_NAME] : C::constant(db\Constant::SESSION_LIFETIME) + CURRENT_TIME, '/');
            } else if (isset($_COOKIE[$projectId])) {
                $hash = $_COOKIE[$projectId];
                $_SESSION[$projectId] = $hash;
            }
        } else {
            $hash = $_SESSION[$projectId];
            setcookie($projectId, $hash, isset($_SESSION[CC::SESSION_TTL_NAME]) ? $_SESSION[CC::SESSION_TTL_NAME] : C::constant(db\Constant::SESSION_LIFETIME) + CURRENT_TIME, '/');
        }

        return self::getByUid($hash, $project);
    }

    /**
     * Get private data by user ID.
     *
     * @param int|array $personId Person ID(s)
     *
     * @return \Modules\Base\Models\PersonPrivate[]|\Modules\Base\Models\PersonPrivate|false
     */
    public static function getByUser($personId)
    {
        return (static::getSelect())
            ->addWhere(static::createWhere(self::PERSON_ID, $personId))
            ->pop(!is_array($personId))
            ->result();
    }

    /**
     * Authenticate user with password.
     *
     * @param string $pass Plain password
     * @param string $login Login username
     *
     * @return \Modules\Base\Models\PersonPrivate|false
     */
    public static function getAuthUser($pass, $login)
    {
        $res = static::getPrivate(static::getSelect(), $login, $pass);
        return is_bool($res) ? false : array_pop($res);
    }

    /**
     * Check authentication attempt count.
     *
     * @param string $pass Plain password
     * @param string $login Login username
     *
     * @return \Modules\Base\Models\PersonPrivate[]|false
     */
    public static function checkAuthUser($pass, $login)
    {
        return static::getPrivate(static::getSelect(\Application\Assistance\Select::COUNT), $login, $pass);
    }

    /**
     * Private authentication method with hash support.
     *
     * Supports multiple hash algorithms:
     * - sha1: SHA1 hash
     * - argon2: Argon2 password verification
     * - default: MD5 via entity table
     *
     * @param \Application\Assistance\Select $select Select object
     * @param string $login Login username
     * @param string $pass Plain password
     *
     * @return \Modules\Base\Models\PersonPrivate[]|false
     */
    private static function getPrivate($select, $login, $pass)
    {
        switch (C::get('hash_algorithm')) {
            case 'sha1':
                $pass = sha1($pass);
                break;
            case 'argon2':
                if ($res = $select->addWhere([
                    static::createWhere(self::PERSON_PRIVATE_LOGIN, $login, Where::LIKE_EQUAL),
                    self::createWhere(
                        (new JoinField())->setBase(Entity::getBase())->setTable(Entity::$_table)->setField(Entity::ENTITY_ENTITY),
                        null,
                        Where::NOT_NULL,
                        [Entity::getBase(), Entity::$_table],
                        [
                            static::createWhere((new JoinField())->setBase(Entity::getBase())->setTable(Entity::$_table)->setField(Entity::ENTITY_ENTITY), (new JoinField())->setBase(self::getBase())->setTable(self::$_table)->setField(self::PERSON_ID)),
                            static::createWhere((new JoinField())->setBase(Entity::getBase())->setTable(Entity::$_table)->setField(Entity::ENTITY_ENTITY_TYPE), self::$_table)
                        ]
                    ),
                    self::createWhere(
                        (new JoinField())->setBase(Person::getBase())->setTable(Person::$_table)->setField(Person::PERSON_ID),
                        null,
                        Where::NOT_NULL,
                        [Person::getBase(), Person::$_table],
                        [
                            static::createWhere((new JoinField())->setBase(Person::getBase())->setTable(Person::$_table)->setField(Person::PERSON_ID), (new JoinField())->setBase(self::getBase())->setTable(self::$_table)->setField(self::PERSON_ID)),
                            static::createWhere((new JoinField())->setBase(Person::getBase())->setTable(Person::$_table)->setField(Person::PROJECT_ID), CURRENT_PROJECT)
                        ]
                    )
                ])->result()
                ) {
                    /** @var \Modules\Base\Models\PersonPrivate[] $res */
                    if (password_verify($pass, $res[array_keys($res)[0]]->getPersonPrivateHash())) {
                        return $res;
                    }
                    return false;
                }
                break;
        }

        // Default: MD5 via entity table
        return $select->addWhere([
            static::createWhere(self::PERSON_PRIVATE_LOGIN, $login, Where::LIKE_EQUAL),
            self::createWhere(
                (new JoinField())->setBase(Entity::getBase())->setTable(Entity::$_table)->setField(Entity::ENTITY_ENTITY),
                null,
                Where::NOT_NULL,
                [Entity::getBase(), Entity::$_table],
                [
                    static::createWhere((new JoinField())->setBase(Entity::getBase())->setTable(Entity::$_table)->setField(Entity::ENTITY_ENTITY), (new JoinField())->setBase(self::getBase())->setTable(self::$_table)->setField(self::PERSON_ID)),
                    static::createWhere((new JoinField())->setBase(Entity::getBase())->setTable(Entity::$_table)->setField(Entity::ENTITY_VALUE), $pass)
                ]
            ),
            self::createWhere(
                (new JoinField())->setBase(Person::getBase())->setTable(Person::$_table)->setField(Person::PERSON_ID),
                null,
                Where::NOT_NULL,
                [Person::getBase(), Person::$_table],
                [
                    static::createWhere((new JoinField())->setBase(Person::getBase())->setTable(Person::$_table)->setField(Person::PERSON_ID), (new JoinField())->setBase(self::getBase())->setTable(self::$_table)->setField(self::PERSON_ID)),
                    static::createWhere((new JoinField())->setBase(Person::getBase())->setTable(Person::$_table)->setField(Person::PROJECT_ID), CURRENT_PROJECT)
                ]
            )
        ])->result();
    }
}