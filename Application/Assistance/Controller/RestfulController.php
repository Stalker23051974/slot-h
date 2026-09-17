<?php

namespace Application\Assistance\Controller;

use \Application\Assistance\View\RestfulView as View;
use \Application\Helpers\Restful as HRF;

class RestfulController
{

    use TraitClass;

    /**
     * @return bool
     */
    public function isRestFul()
    {
        return true;
    }

    public $_api = true;

    public $_access = [];

    public $_view;
    /** @var  \application\assistance\request $_request */
    public $_request;

    public $_rule = null;

    /** @var \Modules\Base\Models\Person $_auth */
    public $_auth = false;
    public $_token = false;

    const IS_GET = 'GET';
    const IS_POST = 'POST';
    const IS_PUT = 'PUT';
    const IS_DELETE = 'DELETE';
    const IS_HEAD = 'HEAD';
    const IS_OPTIONS = 'OPTIONS';
    const IS_PATCH = 'PATCH';

    const EMAIL_PATTERN = "/^([a-z0-9_\.-]+)@([a-z0-9_\.-]+)\.([a-z\.]{2,6})$/";
    const PHONE_PATTERN = "/^([\d]{8,})$/";
    const DATE_PATTERN = "/^([\d]{2}[\-\/]{1}[\d]{2}[\-\/]{1}[\d]{4})$/";

    protected $_responseTemplateOk = [
        'status' => View::STATUS_SUCCESFUL,
        'error' => 0,
        'data' => [],
        'message' => false
    ];

    protected $_responseTemplateError = [
        'status' => View::STATUS_ERROR,
        'error' => '',
        'message' => false
    ];

    const LAST_OPERATION_HASH = 'last';

    /**
     * @param \application\assistance\request $request
     */
    public function __construct($request)
    {
        $this->_view = new View();
        $this->_view->_code = 200;
        $this->_view->_resultCode = 415;
        $this->_view->_message = 'Request is not valid';
        if ($this->_access[$request->getAction()] == $_SERVER['REQUEST_METHOD']) {
            $this->_request = $request;
            $preloader = \Application\Crud::MODULE_FOLDER.'\\' . $request->getModule() . '\\Preloader';
            new $preloader($this->_view, $request);
            $this->{$request->getAction() . \Application\Assistance\Controller\Controller::ACTION_SUFFIX}();
        }
        $this->_view->show();
    }

    /**
     * @param array $data
     * @return array
     */
    public function setAutocompleteResponse($data = [])
    {
        return ['suggestions' => $data];
    }

    /**
     * @param $data
     * @param $message
     * @return array
     */
    public function setOk($data = [], $message = false)
    {
        $response = $this->_responseTemplateOk;
        $response['data'] = $data;
        if (is_string($message)) {
            $response['message'] = $message;
        }
        return $response;
    }

    /**
     * @param $code
     * @param bool $message
     * @param string $append
     * @return array
     */
    public function setError($code, $message = false, $append = '')
    {
        $response = $this->_responseTemplateError;
        $response['error'] = $code;
        if (is_integer($message)) {
            $this->_view->_code = $message;
        } else if (is_array($message)) {
            $response['message'] = sprintf($message[HRF::FIELD_ERROR_MESSAGE], $append);
            $this->_view->_code = $message[HRF::FIELD_ERROR_CODE];
        } else {
            $response['message'] = sprintf($message, $append);
        }
        return $response;
    }

    /**
     * @param $code
     * @param string $append
     */
    public function setErrorByTrigger($code, $append = '')
    {
        $this->_view->_result = $this->setError($code, $this->_rule[HRF::ERROR_LIST][$code][HRF::FIELD_ERROR_MESSAGE], $append);
    }

    /**
     * @param $code
     */
    public function setSystemError($code)
    {
        $this->_view->_result = $this->setError($code, HRF::getErrorDescriptions($code));
    }
}