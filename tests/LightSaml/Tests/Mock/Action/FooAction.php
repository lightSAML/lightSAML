<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests\Mock\Action;

use LightSaml\Action\ActionInterface;
use LightSaml\Context\ContextInterface;

class FooAction implements ActionInterface
{
    /**
     * @return void
     */
    public function execute(ContextInterface $context)
    {
        // foo
    }
}
