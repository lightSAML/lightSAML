<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Builder\Profile;

interface ProfileBuilderInterface
{
    /**
     * @return \LightSaml\Action\CompositeAction
     */
    public function buildAction();

    /**
     * @return \LightSaml\Context\Profile\ProfileContext
     */
    public function buildContext();
}
