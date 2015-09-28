<?php

namespace LightSaml\Action;

use LightSaml\Context\ContextInterface;

interface ActionInterface
{
    /**
     * @param ContextInterface $context
     *
     * @return void
     */
    public function execute(ContextInterface $context);
}
