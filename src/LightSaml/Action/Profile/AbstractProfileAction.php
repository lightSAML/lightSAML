<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Action\Profile;

use LightSaml\Action\ActionInterface;
use LightSaml\Context\ContextInterface;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Error\LightSamlContextException;
use Psr\Log\LoggerInterface;

abstract class AbstractProfileAction implements ActionInterface
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
     *
     * @return void
     */
    public function execute(ContextInterface $context)
    {
        if ($context instanceof ProfileContext) {
            $this->doExecute($context);
        } else {
            $message = sprintf('Expected ProfileContext but got %s', get_class($context));
            $this->logger->emergency($message, array('context' => $context));
            throw new LightSamlContextException($context, $message);
        }
    }

    abstract protected function doExecute(ProfileContext $context);
}
