<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
    public function __construct(ContextInterface $context, $message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->context = $context;
    }

    /**
     * @return ContextInterface
     */
    public function getContext()
    {
        return $this->context;
    }
}
