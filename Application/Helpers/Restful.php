<?php

/**
 * Class not used in this project - it was prototype for wono.io (project 8)
 */

namespace Application\Helpers;

use \Application\Assistance\Controller\RestfulController as RFC;

use \Modules\Base\Models\DbTables as bDb;

class Restful
{

    const API_VERSION = '1';

    const FIELD_METHOD = 'method';
    const FIELD_ERROR_CODE = 'error_code';
    const FIELD_ERROR_MESSAGE = 'error_message';
    const FIELD_ERROR_DESCRIPTION = 'error_description';
    const FIELD_PARAMS = 'params';
    const FIELD_EVENTS = 'events';
    const FIELD_MESSAGE = 'message';
    const FIELD_HTTP_CODE = 'code';
    const FIELD_TYPE = 'type';
    const FIELD_ENUM = 'enum';

    const AUTHORIZATION = 'authorization';
    const REQUIRED = 'required';
    const LENGTH = 'length';
    const ERROR = 'error';
    const ZERO = 'zero';
    const DESCRIPTION = 'description';
    const ERROR_LIST = 'error_list';
    const USE_IN_SDK = 'use';

    const TYPE_STRING = 'string';
    const TYPE_INT = 'int';
    const TYPE_BOOL = 'bool';
    const TYPE_DATE = 'date';
    const TYPE_EMAIL = 'email';
    const TYPE_PHONE = 'phone';
    const TYPE_FILE = 'file';
    const TYPE_ENUM = 'enum';

    public static function getDefaultByTypes()
    {
        return [
            self::TYPE_STRING => \Application\Helpers\Line::generatePass(5, 5, 0)['pass'],
            self::TYPE_INT => \Application\Helpers\Line::generatePass(0, 0, 6)['pass'],
            self::TYPE_BOOL => true,
            self::TYPE_DATE => \Application\Helpers\Date::dbDateTime(),
            self::TYPE_EMAIL => \Application\Helpers\Line::generatePass(7, 0, 0)['pass'] . '@' . \Application\Helpers\Line::generatePass(5, 0, 0)['pass'] . '.' . \Application\Helpers\Line::generatePass(3, 0, 0)['pass'],
            self::TYPE_PHONE => \Application\Helpers\Line::generatePass(0, 0, 10)['pass'],
            self::TYPE_FILE => '',
            self::TYPE_ENUM => ''
        ];
    }

    const HTTP_RESPONSE_100 = 100;
    const HTTP_RESPONSE_200 = 200;
    const HTTP_RESPONSE_201 = 201;
    const HTTP_RESPONSE_202 = 202;
    const HTTP_RESPONSE_203 = 203;
    const HTTP_RESPONSE_204 = 204;
    const HTTP_RESPONSE_208 = 208;
    const HTTP_RESPONSE_304 = 304;
    const HTTP_RESPONSE_400 = 400;
    const HTTP_RESPONSE_401 = 401;
    const HTTP_RESPONSE_402 = 402;
    const HTTP_RESPONSE_403 = 403;
    const HTTP_RESPONSE_404 = 404;
    const HTTP_RESPONSE_405 = 405;
    const HTTP_RESPONSE_406 = 406;
    const HTTP_RESPONSE_409 = 409;
    const HTTP_RESPONSE_410 = 410;
    const HTTP_RESPONSE_415 = 415;
    const HTTP_RESPONSE_416 = 416;
    const HTTP_RESPONSE_417 = 417;
    const HTTP_RESPONSE_418 = 418;
    const HTTP_RESPONSE_423 = 423;
    const HTTP_RESPONSE_426 = 426;
    const HTTP_RESPONSE_451 = 451;
    const HTTP_RESPONSE_500 = 500;
    const HTTP_RESPONSE_503 = 503;
    const HTTP_RESPONSE_511 = 511;
    const HTTP_RESPONSE_523 = 523;

    /*
    100 Continue
    200 OK
    201 Created
    202 Accepted
    203 Non-Authoritative Information
    204 No Content
    208 Already Reported
    304 Not Modify
    400 Bad Request
    401 Unauthorized
    402 Payment Required
    403 Forbidden
    404 Not Found
    405 Method Not Allowed
    406 Not Acceptable
    409 Conflict
    410 Gone
    415 Unsupported Media Type
    423 Locked
    426 Upgrade Required
    451 Unavailable For Legal Reasons
    500 Internal Server Error
    503 Service Unavailable
    511 Network Authentication Required
    523 Origin Is Unreachable
    */
    public static function getResponse($code = false)
    {
        $source = [
            self::HTTP_RESPONSE_100 => '100 Continue',
            self::HTTP_RESPONSE_200 => '200 OK',
            self::HTTP_RESPONSE_201 => '201 Created',
            self::HTTP_RESPONSE_202 => '202 Accepted',
            self::HTTP_RESPONSE_203 => '203 Non-Authoritative Information',
            self::HTTP_RESPONSE_204 => '204 No Content',
            self::HTTP_RESPONSE_208 => '208 Already Reported',
            self::HTTP_RESPONSE_304 => '304 Not Modify',
            self::HTTP_RESPONSE_400 => '400 Bad Request',
            self::HTTP_RESPONSE_401 => '401 Unauthorized',
            self::HTTP_RESPONSE_402 => '402 Payment Required',
            self::HTTP_RESPONSE_403 => '403 Forbidden',
            self::HTTP_RESPONSE_404 => '404 Not Found',
            self::HTTP_RESPONSE_405 => '405 Method Not Allowed',
            self::HTTP_RESPONSE_406 => '406 Not Acceptable',
            self::HTTP_RESPONSE_409 => '409 Conflict',
            self::HTTP_RESPONSE_410 => '410 Gone',
            self::HTTP_RESPONSE_415 => '415 Unsupported Media Type',
            self::HTTP_RESPONSE_423 => '423 Locked',
            self::HTTP_RESPONSE_426 => '426 Upgrade Required',
            self::HTTP_RESPONSE_451 => '451 Unavailable For Legal Reasons',
            self::HTTP_RESPONSE_500 => '500 Internal Server Error',
            self::HTTP_RESPONSE_503 => '503 Service Unavailable',
            self::HTTP_RESPONSE_511 => '511 Network Authentication Required',
            self::HTTP_RESPONSE_523 => '523 Origin Is Unreachable',
        ];
        return false === $code ? $source : $source[$code];
    }

    public static $_successResponse = null;

    public static function getSuccessResponse()
    {
        if (is_null(self::$_successResponse)) {
            self::$_successResponse = [
                self::FIELD_ERROR_MESSAGE => [
                    self::FIELD_ERROR_CODE => self::HTTP_RESPONSE_200,
                    self::FIELD_ERROR_MESSAGE => self::getErrorDescriptions(self::SUCCESSFUL)
                ],
                self::FIELD_ERROR_DESCRIPTION => 'Success'
            ];
        }
        return self::$_successResponse;
    }

    public static function getMethods()
    {
        return [
            RFC::IS_PUT,
            RFC::IS_DELETE,
            RFC::IS_POST,
            RFC::IS_GET,
            RFC::IS_HEAD,
            RFC::IS_OPTIONS,
            RFC::IS_PATCH
        ];
    }

    public static function getProtectedMethods()
    {
        return [
            RFC::IS_POST,
            RFC::IS_PUT,
            RFC::IS_DELETE
        ];
    }

    const SUCCESSFUL = 0;
    const ERROR_UNKNOWN_ACTION = 401;
    const ERROR_UNKNOWN_OBJECT = 402;
    const ERROR_DENIED = 403;

    public static function getErrorDescriptions($code = false)
    {
        $res = [
            self::SUCCESSFUL => '',
            self::ERROR_UNKNOWN_ACTION => 'Unknown action',
            self::ERROR_UNKNOWN_OBJECT => 'Unknown object',
            self::ERROR_DENIED => 'Rejected'
        ];
        return false === $code ? $res : $res[$code];
    }
}