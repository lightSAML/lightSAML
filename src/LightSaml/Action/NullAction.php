<?php

namespace LightSaml\Action;

use LightSaml\Context\AbstractContext;

class NullAction implements ActionInterface
{
    /**
     * @param AbstractContext $context
     *
     * @return void
     */
    public function execute(AbstractContext $context)
    {
        // null
    }
}
