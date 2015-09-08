<?php

namespace LightSaml\Action;

use LightSaml\Context\AbstractContext;

abstract class WrappedAction implements ActionInterface
{
    /**
     * @var ActionInterface
     */
    protected $action;

    /**
     * @param ActionInterface $action
     */
    public function __construct(ActionInterface $action)
    {
        $this->action = $action;
    }

    /**
     * @param AbstractContext $context
     *
     * @return void
     */
    public function execute(AbstractContext $context)
    {
        $this->beforeAction($context);
        $this->action->execute($context);
        $this->afterAction($context);
    }

    /**
     * @param AbstractContext $context
     */
    abstract protected function beforeAction(AbstractContext $context);

    /**
     * @param AbstractContext $context
     */
    abstract protected function afterAction(AbstractContext $context);
}
