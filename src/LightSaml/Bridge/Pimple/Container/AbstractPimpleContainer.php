<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Bridge\Pimple\Container;

use Pimple\Container;

abstract class AbstractPimpleContainer
{
    /** @var Container */
    protected $pimple;

    /**
     * @param Container $pimple
     */
    public function __construct(Container $pimple)
    {
        $this->pimple = $pimple;
    }

    /**
     * @return Container
     */
    public function getPimple()
    {
        return $this->pimple;
    }
}
