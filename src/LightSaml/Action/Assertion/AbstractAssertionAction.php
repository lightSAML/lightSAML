<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Action\Assertion;

use LightSaml\Action\ActionInterface;
use LightSaml\Context\ContextInterface;
use LightSaml\Context\Profile\AssertionContext;
use LightSaml\Error\LightSamlContextException;
use Psr\Log\LoggerInterface;

abstract class AbstractAssertionAction implements ActionInterface
{
    /** @var LoggerInterface */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param ContextInterface $context
     */
    public function execute(ContextInterface $context)
    {
        if ($context instanceof AssertionContext) {
            $this->doExecute($context);
        } else {
            throw new LightSamlContextException($context, 'Expected AssertionContext');
        }
    }

    /**
     * @param AssertionContext $context
     */
    abstract protected function doExecute(AssertionContext $context);
}
