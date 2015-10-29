<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Action;

use LightSaml\Context\ContextInterface;
use Psr\Log\LoggerInterface;

class LoggableAction extends WrappedAction
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ActionInterface $action
     * @param LoggerInterface $logger
     */
    public function __construct(ActionInterface $action, LoggerInterface $logger)
    {
        parent::__construct($action);

        $this->logger = $logger;
    }

    /**
     * @param ContextInterface $context
     */
    protected function beforeAction(ContextInterface $context)
    {
        $this->logger->debug(sprintf('Executing action "%s"', get_class($this->action)), array(
            'context' => $context,
            'action' => $this->action,
        ));
    }

    /**
     * @param ContextInterface $context
     */
    protected function afterAction(ContextInterface $context)
    {
    }
}
