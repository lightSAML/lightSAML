<?php

namespace LightSaml\Error;

use LightSaml\Context\ContextInterface;

class LightSamlContextException extends LightSamlException
{
    /** @var ContextInterface */
    protected $context;

    /**
     * @param ContextInterface $context
     * @param string           $message
     * @param int              $code
     * @param \Exception       $previous
     */
    public function __construct(ContextInterface $context, $message = "", $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return ContextInterface
     */
    public function getContext()
    {
        return $this->context;
    }
}
