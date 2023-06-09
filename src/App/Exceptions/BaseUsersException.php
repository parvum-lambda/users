<?php

namespace App\Exceptions;

class BaseUsersException extends \Exception
{
    private string $codeStr;
    private int $httpStatusCode;

    public function __construct(UsersExceptions $code, private readonly string $reason = '')
    {
        $this->code = $code;

        $error = UsersExceptions::getErrorFromCode($this->code);

        $this->codeStr = $this->code->name;
        $this->message = $error['message'];
        $this->httpStatusCode = $error['http_status_code'];

        parent::__construct($this->getFullMessage(), $code->value);
    }

    public function getFullMessage() : string
    {
        $reason = $this->reason ? ". $this->reason " : '';

        return "$this->message $reason({$this->code->value})";
    }

    /**
     * @return string
     */
    public function getCodeStr() : string
    {
        return $this->codeStr;
    }

    /**
     * @return int
     */
    public function getHttpStatusCode() : int
    {
        return $this->httpStatusCode;
    }
}
