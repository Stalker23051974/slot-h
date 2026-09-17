<?php

namespace Application\Helpers\Mail;

interface InterfaceMail
{

    /**
     * @param $template
     * @param $params
     * @param $data
     * @return mixed
     */
    public function send($template, $params, $data, $personId);

    /**
     * @param $message
     * @param $title
     * @param $file
     * @return mixed
     */
    public function setLog($message, $title, $file);
}