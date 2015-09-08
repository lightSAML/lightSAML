<?php

namespace LightSaml\Builder\Context;

use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Error\LightSamlException;
use LightSaml\Provider\EntityDescriptor\EntityDescriptorProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class ProfileContextBuilder
{
    /** @var Request */
    private $request;

    /** @var EntityDescriptorProviderInterface */
    private $ownEntityDescriptorProvider;

    /** @var  int */
    private $profileId;

    /** @var string */
    private $profileRole;

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request $request
     *
     * @return ProfileContextBuilder
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return EntityDescriptorProviderInterface
     */
    public function getOwnEntityDescriptorProvider()
    {
        return $this->ownEntityDescriptorProvider;
    }

    /**
     * @param EntityDescriptorProviderInterface $ownEntityDescriptorProvider
     *
     * @return ProfileContextBuilder
     */
    public function setOwnEntityDescriptorProvider(EntityDescriptorProviderInterface $ownEntityDescriptorProvider)
    {
        $this->ownEntityDescriptorProvider = $ownEntityDescriptorProvider;

        return $this;
    }

    /**
     * @return int
     */
    public function getProfileId()
    {
        return $this->profileId;
    }

    /**
     * @param int $profileId
     *
     * @return ProfileContextBuilder
     */
    public function setProfileId($profileId)
    {
        $this->profileId = $profileId;

        return $this;
    }

    /**
     * @return string
     */
    public function getProfileRole()
    {
        return $this->profileRole;
    }

    /**
     * @param string $profileRole
     *
     * @return ProfileContextBuilder
     */
    public function setProfileRole($profileRole)
    {
        $this->profileRole = $profileRole;

        return $this;
    }

    /**
     * @return ProfileContext
     */
    public function build()
    {
        if (null === $this->request) {
            throw new LightSamlException('HTTP Request not set');
        }
        if (null === $this->ownEntityDescriptorProvider) {
            throw new LightSamlException('Own EntityDescriptor not set');
        }
        if (null === $this->profileId) {
            throw new LightSamlException('ProfileID not set');
        }
        if (null === $this->profileRole) {
            throw new LightSamlException('Profile role not set');
        }

        $result = new ProfileContext($this->profileId, $this->profileRole);

        $result->getHttpRequestContext()->setRequest($this->request);
        $result->getOwnEntityContext()->setEntityDescriptor($this->ownEntityDescriptorProvider->get());

        return $result;
    }
}
