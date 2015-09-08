<?php

namespace LightSaml\Error;

use LightSaml\Model\Protocol\StatusResponse;

class LightSamlAuthenticationException extends LightSamlValidationException
{
    /** @var StatusResponse  */
    protected $response;

    /**
     * @param StatusResponse $response
     * @param string         $message
     * @param int            $code
     * @param \Exception     $previous
     */
    public function __construct(StatusResponse $response, $message = "", $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->response = $response;
    }

    /**
     * @return \LightSaml\Model\Protocol\Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
