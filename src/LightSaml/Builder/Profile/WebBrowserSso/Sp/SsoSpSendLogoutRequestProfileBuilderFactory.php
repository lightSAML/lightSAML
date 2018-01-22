<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Builder\Profile\WebBrowserSso\Sp;

use LightSaml\Build\Container\BuildContainerInterface;

class SsoSpSendLogoutRequestProfileBuilderFactory
{
    /** @var BuildContainerInterface */
    protected $buildContainer;

    /**
     * @param BuildContainerInterface $buildContainer
     */
    public function __construct(BuildContainerInterface $buildContainer)
    {
        $this->buildContainer = $buildContainer;
    }

    /**
     * @param string $idpEntityId
     *
     * @return SsoSpSendLogoutRequestProfileBuilder
     */
    public function get($idpEntityId)
    {
        return new SsoSpSendLogoutRequestProfileBuilder($this->buildContainer, $idpEntityId);
    }
}
