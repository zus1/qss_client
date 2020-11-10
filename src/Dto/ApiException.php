<?php

namespace App\Dto;

class ApiException
{
    private String $message;
    private Integer $code;

    /**
     * @return String
     */
    public function getMessage(): String
    {
        return $this->message;
    }

    /**
     * @param String $message
     */
    public function setMessage(String $message): void
    {
        $this->message = $message;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @param int $code
     */
    public function setCode(int $code): void
    {
        $this->code = $code;
    }


}