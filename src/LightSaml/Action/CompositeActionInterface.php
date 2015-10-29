<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
