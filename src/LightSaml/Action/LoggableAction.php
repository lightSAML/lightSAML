<?php

namespace LightSaml\Action;

use LightSaml\Context\AbstractContext;
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
     * @param AbstractContext $context
     */
    protected function beforeAction(AbstractContext $context)
    {
        $this->logger->debug(sprintf('Executing action "%s"', get_class($this->action)), array(
            'context' => $context,
            'action' => $this->action,
        ));
    }

    /**
     * @param AbstractContext $context
     */
    protected function afterAction(AbstractContext $context)
    {

    }
}
