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

use LightSaml\Build\Container\BuildContainerInterface;
use LightSaml\Builder\Context\ProfileContextBuilder;

abstract class AbstractProfileBuilder implements ProfileBuilderInterface
{
    /** @var BuildContainerInterface */
    protected $container;

    /**
     * @param BuildContainerInterface $buildContainer
     */
    public function __construct(BuildContainerInterface $buildContainer)
    {
        $this->container = $buildContainer;
    }

    /**
     * @return \LightSaml\Action\CompositeAction
     */
    public function buildAction()
    {
        return $this->getActionBuilder()->build();
    }

    /**
     * @return \LightSaml\Context\Profile\ProfileContext
     */
    public function buildContext()
    {
        $builder = new ProfileContextBuilder();
        $builder
            ->setProfileId($this->getProfileId())
            ->setRequest($this->container->getSystemContainer()->getRequest())
            ->setProfileRole($this->getProfileRole())
            ->setOwnEntityDescriptorProvider($this->container->getOwnContainer()->getOwnEntityDescriptorProvider())
        ;

        return $builder->build();
    }

    /**
     * @return string
     */
    abstract protected function getProfileId();

    /**
     * @return string
     */
    abstract protected function getProfileRole();

    /**
     * @return \LightSaml\Builder\Action\ActionBuilderInterface
     */
    abstract protected function getActionBuilder();
}
