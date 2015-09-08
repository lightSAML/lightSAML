<?php

namespace LightSaml\Builder\Action;

use LightSaml\Action\ActionInterface;

interface ActionBuilderInterface
{
    /**
     * @return ActionInterface
     */
    public function build();
}
