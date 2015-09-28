<?php

namespace LightSaml\Action;

use LightSaml\Context\ContextInterface;

class NullAction implements ActionInterface
{
    /**
     * @param ContextInterface $context
     *
     * @return void
     */
    public function execute(ContextInterface $context)
    {
        // null
    }
}
