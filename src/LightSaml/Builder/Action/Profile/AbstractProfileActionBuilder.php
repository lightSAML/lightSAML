<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Builder\Action\Profile;

use LightSaml\Build\Container\BuildContainerInterface;
use LightSaml\Builder\Action\CompositeActionBuilder;
use LightSaml\Error\LightSamlBuildException;

abstract class AbstractProfileActionBuilder extends CompositeActionBuilder
{
    /** @var BuildContainerInterface */
    protected $buildContainer;

    /** @var bool */
    private $initialized = false;

    /**
     * @param BuildContainerInterface $buildContainer
     */
    public function __construct(BuildContainerInterface $buildContainer)
    {
        $this->buildContainer = $buildContainer;
    }

    /**
     * @return void
     */
    public function init()
    {
        if ($this->initialized) {
            throw new LightSamlBuildException('Already initialized');
        }

        $this->doInitialize();

        $this->initialized = true;
    }

    /**
     * @return void
     */
    abstract protected function doInitialize();

    /**
     * @return \LightSaml\Action\ActionInterface
     */
    public function build()
    {
        if (false === $this->initialized) {
            $this->init();
        }

        return parent::build();
    }
}
