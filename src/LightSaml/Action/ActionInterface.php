<?php

namespace LightSaml\Action;

use LightSaml\Context\AbstractContext;

interface ActionInterface
{
    /**
     * @param AbstractContext $context
     *
     * @return void
     */
    public function execute(AbstractContext $context);
}
