<?php

namespace Voidpointers\Etsy\Exceptions;

class ResponseException extends \Exception
{
    /**
     * @var mixed
     */
    private $response;

    /**
     * EtsyResponseException constructor.
     * @param string $message
     * @param mixed $response
     */
    public function __construct($message, $response = [])
    {
        $this->response = $response;
        parent::__construct($message);
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }
}

