<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests;

use LightSaml\Context\Profile\AssertionContext;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Criteria\CriteriaSet;
use LightSaml\Model\Assertion\Assertion;
use LightSaml\Model\Metadata\Endpoint;
use LightSaml\Profile\Profiles;
use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Psr\Log\LoggerInterface
     */
    public function getLoggerMock()
    {
        return $this->getMockBuilder(\Psr\Log\LoggerInterface::class)->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\LightSaml\Provider\TimeProvider\TimeProviderInterface
     */
    public function getTimeProviderMock()
    {
        return $this->getMockBuilder(\LightSaml\Provider\TimeProvider\TimeProviderInterface::class)->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\LightSaml\Model\Metadata\EndpointReference
     */
    public function getEndpointReferenceMock(Endpoint $endpoint)
    {
        $endpointReferenceMock = $this->getMockBuilder(\LightSaml\Model\Metadata\EndpointReference::class)->disableOriginalConstructor()->getMock();

        $endpointReferenceMock->expects($this->any())
            ->method('getEndpoint')
            ->willReturn($endpoint);

        return $endpointReferenceMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\LightSaml\Resolver\Endpoint\EndpointResolverInterface
     */
    public function getEndpointResolverMock()
    {
        return $this->getMockBuilder(\LightSaml\Resolver\Endpoint\EndpointResolverInterface::class)->getMock();
    }

    /**
     * @param string $profileId
     * @param string $ownRole
     *
     * @return ProfileContext
     */
    public function getProfileContext($profileId = Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, $ownRole = ProfileContext::ROLE_IDP)
    {
        $context = new ProfileContext($profileId, $ownRole);

        return $context;
    }

    /**
     * @return AssertionContext
     */
    public function getAssertionContext(Assertion $assertion)
    {
        $context = new AssertionContext();

        if ($assertion) {
            $context->setAssertion($assertion);
        }

        return $context;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\LightSaml\Store\Request\RequestStateStoreInterface
     */
    public function getRequestStateStoreMock()
    {
        return $this->getMockBuilder(\LightSaml\Store\Request\RequestStateStoreInterface::class)->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\LightSaml\Binding\BindingFactoryInterface
     */
    public function getBindingFactoryMock()
    {
        return $this->getMockBuilder(\LightSaml\Binding\BindingFactoryInterface::class)->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\LightSaml\Binding\AbstractBinding
     */
    public function getBindingMock()
    {
        return $this->getMockForAbstractClass(\LightSaml\Binding\AbstractBinding::class);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\LightSaml\Resolver\Signature\SignatureResolverInterface
     */
    public function getSignatureResolverMock()
    {
        return $this->getMockBuilder(\LightSaml\Resolver\Signature\SignatureResolverInterface::class)->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\LightSaml\Credential\X509Certificate
     */
    public function getX509CertificateMock()
    {
        return $this->getMockBuilder(\LightSaml\Credential\X509Certificate::class)->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\LightSaml\Validator\Model\Assertion\AssertionValidatorInterface
     */
    public function getAssertionValidatorMock()
    {
        return $this->getMockBuilder(\LightSaml\Validator\Model\Assertion\AssertionValidatorInterface::class)->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\LightSaml\Store\EntityDescriptor\EntityDescriptorStoreInterface
     */
    public function getEntityDescriptorStoreMock()
    {
        return $this->getMockBuilder(\LightSaml\Store\EntityDescriptor\EntityDescriptorStoreInterface::class)->getMock();
    }

    /**
     * @param string $class
     * @param string $getter
     * @param string $value
     */
    public function assertCriteria(CriteriaSet $criteriaSet, $class, $getter, $value)
    {
        $this->assertTrue($criteriaSet->has($class));
        $criteria = $criteriaSet->getSingle($class);
        if ($getter) {
            $this->assertEquals($value, $criteria->{$getter}());
        }
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\LightSaml\Store\Id\IdStoreInterface
     */
    public function getIdStoreMock()
    {
        return $this->getMockBuilder(\LightSaml\Store\Id\IdStoreInterface::class)->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\LightSaml\Validator\Model\Assertion\AssertionTimeValidatorInterface
     */
    public function getAssertionTimeValidatorMock()
    {
        return $this->getMockBuilder(\LightSaml\Validator\Model\Assertion\AssertionTimeValidatorInterface::class)->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\LightSaml\Criteria\CriteriaInterface
     */
    public function getCriteriaMock()
    {
        return $this->getMockBuilder(\LightSaml\Criteria\CriteriaInterface::class)->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\LightSaml\Resolver\Credential\CredentialResolverInterface
     */
    public function getCredentialResolverMock()
    {
        return $this->getMockBuilder(\LightSaml\Resolver\Credential\CredentialResolverInterface::class)->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\LightSaml\Credential\X509CredentialInterface
     */
    public function getX509CredentialMock()
    {
        return $this->getMockBuilder(\LightSaml\Credential\X509CredentialInterface::class)->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    public function getSessionMock()
    {
        return $this->getMockBuilder(\Symfony\Component\HttpFoundation\Session\SessionInterface::class)->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\RobRichards\XMLSecLibs\XMLSecurityKey
     */
    public function getXmlSecurityKeyMock()
    {
        return $this->getMockBuilder(\RobRichards\XMLSecLibs\XMLSecurityKey::class)
            ->disableOriginalConstructor()
            ->getMock()
            ;
    }
}
