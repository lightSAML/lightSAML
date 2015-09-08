<?php

namespace LightSaml\Error;

use LightSaml\Context\AbstractContext;

class LightSamlContextException extends LightSamlException
{
    /** @var  AbstractContext */
    protected $context;

    /**
     * @param AbstractContext $context
     * @param string          $message
     * @param int             $code
     * @param \Exception      $previous
     */
    public function __construct(AbstractContext $context, $message = "", $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return AbstractContext
     */
    public function getContext()
    {
        return $this->context;
    }
}
