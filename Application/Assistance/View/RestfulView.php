<?php

namespace Application\Assistance\View;

class RestfulView
{
    public $_result;

    public $_resultCode;

    public $_message;

    public $_responseArray = false;

    public $_code = false;

    /** @var bool|\Modules\Base\Models\Person $currentPerson */
    public $currentPerson = false;

    const STATUS_SUCCESFUL = 'ok';
    const STATUS_ERROR = 'error';

    /**
     * generate html
     */
    public function show()
    {
        header('Access-Control-Allow-Origin: *');
        header('HTTP/1.1 ' . \Application\Helpers\Restful::getResponse(false !== $this->_code ? $this->_code : \Application\Helpers\Restful::HTTP_RESPONSE_200));
        $codes = \Application\Helpers\Restful::getErrorDescriptions();
        $message = (isset($codes[$this->_resultCode]) ? $codes[$this->_resultCode] : '');
        if (!empty($this->_message)) {
            $message .= (!empty($message) ? ' (' : '').$this->_message.(!empty($message) ? ')' : '');
        }
        echo dfJsonEncode([
            'code' => $this->_resultCode,
            'data' => $this->_result,
            'message' => $message
        ]);
    }
}