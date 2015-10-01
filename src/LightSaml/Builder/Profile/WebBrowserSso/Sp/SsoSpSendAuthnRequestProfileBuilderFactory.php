<?php

namespace LightSaml\Builder\Profile\WebBrowserSso\Sp;

use LightSaml\Build\Container\BuildContainerInterface;

class SsoSpSendAuthnRequestProfileBuilderFactory
{
    /** @var BuildContainerInterface */
    private $buildContainer;

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
     * @return SsoSpSendAuthnRequestProfileBuilder
     */
    public function get($idpEntityId)
    {
        return new SsoSpSendAuthnRequestProfileBuilder($this->buildContainer, $idpEntityId);
    }
}
