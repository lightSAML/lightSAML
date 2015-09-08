<?php

namespace LightSaml\Action;

interface ActionWrapperInterface
{
    /**
     * @param ActionInterface $action
     *
     * @return ActionInterface
     */
    public function wrap(ActionInterface $action);
}
