<?php

namespace LightSaml\Action;

use Psr\Log\LoggerInterface;

class ActionLogWrapper implements ActionWrapperInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param ActionInterface $action
     *
     * @return ActionInterface
     */
    public function wrap(ActionInterface $action)
    {
        return new LoggableAction($action, $this->logger);
    }
}
