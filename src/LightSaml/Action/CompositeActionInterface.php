<?php

namespace LightSaml\Action;

interface CompositeActionInterface extends ActionInterface
{
    /**
     * @param ActionInterface $action
     *
     * @return CompositeActionInterface
     */
    public function add(ActionInterface $action);

    /**
     * @param callable $callable
     *
     * @return ActionInterface|null
     */
    public function map($callable);
}
