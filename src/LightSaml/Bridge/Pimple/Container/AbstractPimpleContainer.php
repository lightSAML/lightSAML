<?php

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
