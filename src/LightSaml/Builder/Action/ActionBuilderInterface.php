<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Builder\Action;

use LightSaml\Action\ActionInterface;

interface ActionBuilderInterface
{
    /**
     * @return ActionInterface
     */
    public function build();
}
