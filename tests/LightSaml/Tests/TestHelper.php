<?php

namespace LightSaml\Tests;

use LightSaml\Context\Profile\AssertionContext;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Criteria\CriteriaSet;
use LightSaml\Model\Assertion\Assertion;
use LightSaml\Model\Metadata\Endpoint;
use LightSaml\Profile\Profiles;

abstract class TestHelper
{
    /**
     * @param \PHPUnit_Framework_TestCase $test
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Psr\Log\LoggerInterface
     */
    public static function getLoggerMock(\PHPUnit_Framework_TestCase $test)
    {
        return $test->getMock(\Psr\Log\LoggerInterface::class);
    }

    /**
     * @param \PHPUnit_Framework_TestCase $test
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Provider\TimeProvider\TimeProviderInterface
     */
    public static function getTimeProviderMock(\PHPUnit_Framework_TestCase $test)
    {
        return $test->getMock(\LightSaml\Provider\TimeProvider\TimeProviderInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Model\Metadata\EndpointReference
     */
    public static function getEndpointReferenceMock(\PHPUnit_Framework_TestCase $test, Endpoint $endpoint)
    {
        $endpointReferenceMock = $test->getMockBuilder(\LightSaml\Model\Metadata\EndpointReference::class)->disableOriginalConstructor()->getMock();

        $endpointReferenceMock->expects($test->any())
            ->method('getEndpoint')
            ->willReturn($endpoint);

        return $endpointReferenceMock;
    }

    /**
     * @param \PHPUnit_Framework_TestCase $test
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Resolver\Endpoint\EndpointResolverInterface
     */
    public static function getEndpointResolverMock(\PHPUnit_Framework_TestCase $test)
    {
        return $test->getMock(\LightSaml\Resolver\Endpoint\EndpointResolverInterface::class);
    }

    /**
     * @param string $profileId
     * @param string $ownRole
     *
     * @return ProfileContext
     */
    public static function getProfileContext($profileId = Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, $ownRole = ProfileContext::ROLE_IDP)
    {
        $context = new ProfileContext($profileId, $ownRole);

        return $context;
    }

    /**
     * @param Assertion $assertion
     *
     * @return AssertionContext
     */
    public static function getAssertionContext(Assertion $assertion)
    {
        $context = new AssertionContext();

        if ($assertion) {
            $context->setAssertion($assertion);
        }

        return $context;
    }

    /**
     * @param \PHPUnit_Framework_TestCase $test
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Store\Request\RequestStateStoreInterface
     */
    public static function getRequestStateStoreMock(\PHPUnit_Framework_TestCase $test)
    {
        return $test->getMock(\LightSaml\Store\Request\RequestStateStoreInterface::class);
    }

    /**
     * @param \PHPUnit_Framework_TestCase $test
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Binding\BindingFactoryInterface
     */
    public static function getBindingFactoryMock(\PHPUnit_Framework_TestCase $test)
    {
        return $test->getMock(\LightSaml\Binding\BindingFactoryInterface::class);
    }

    /**
     * @param \PHPUnit_Framework_TestCase $test
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Binding\AbstractBinding
     */
    public static function getBindingMock(\PHPUnit_Framework_TestCase $test)
    {
        return $test->getMockForAbstractClass(\LightSaml\Binding\AbstractBinding::class);
    }

    /**
     * @param \PHPUnit_Framework_TestCase $test
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Resolver\Signature\SignatureResolverInterface
     */
    public static function getSignatureResolverMock(\PHPUnit_Framework_TestCase $test)
    {
        return $test->getMock(\LightSaml\Resolver\Signature\SignatureResolverInterface::class);
    }

    /**
     * @param \PHPUnit_Framework_TestCase $test
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Credential\X509Certificate
     */
    public static function getX509CertificateMock(\PHPUnit_Framework_TestCase $test)
    {
        return $test->getMock(\LightSaml\Credential\X509Certificate::class);
    }

    /**
     * @param \PHPUnit_Framework_TestCase $test
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Validator\Model\Assertion\AssertionValidatorInterface
     */
    public static function getAssertionValidatorMock(\PHPUnit_Framework_TestCase $test)
    {
        return $test->getMock(\LightSaml\Validator\Model\Assertion\AssertionValidatorInterface::class);
    }

    /**
     * @param \PHPUnit_Framework_TestCase $test
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Store\EntityDescriptor\EntityDescriptorStoreInterface
     */
    public static function getEntityDescriptorStoreMock(\PHPUnit_Framework_TestCase $test)
    {
        return $test->getMock(\LightSaml\Store\EntityDescriptor\EntityDescriptorStoreInterface::class);
    }

    /**
     * @param \PHPUnit_Framework_TestCase $test
     * @param CriteriaSet                 $criteriaSet
     * @param string                      $class
     * @param string                      $getter
     * @param string                      $value
     */
    public static function assertCriteria(\PHPUnit_Framework_TestCase $test, CriteriaSet $criteriaSet, $class, $getter, $value)
    {
        $test->assertTrue($criteriaSet->has($class));
        $criteria = $criteriaSet->getSingle($class);
        if ($getter) {
            $test->assertEquals($value, $criteria->{$getter}());
        }
    }

    /**
     * @param \PHPUnit_Framework_TestCase $test
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Store\Id\IdStoreInterface
     */
    public static function getIdStoreMock(\PHPUnit_Framework_TestCase $test)
    {
        return $test->getMock(\LightSaml\Store\Id\IdStoreInterface::class);
    }

    /**
     * @param \PHPUnit_Framework_TestCase $test
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Validator\Model\Assertion\AssertionTimeValidatorInterface
     */
    public static function getAssertionTimeValidatorMock(\PHPUnit_Framework_TestCase $test)
    {
        return $test->getMock(\LightSaml\Validator\Model\Assertion\AssertionTimeValidatorInterface::class);
    }

    /**
     * @param \PHPUnit_Framework_TestCase $test
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Criteria\CriteriaInterface
     */
    public static function getCriteriaMock(\PHPUnit_Framework_TestCase $test)
    {
        return $test->getMock(\LightSaml\Criteria\CriteriaInterface::class);
    }

    /**
     * @param \PHPUnit_Framework_TestCase $test
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Resolver\Credential\CredentialResolverInterface
     */
    public static function getCredentialResolverMock(\PHPUnit_Framework_TestCase $test)
    {
        return $test->getMock(\LightSaml\Resolver\Credential\CredentialResolverInterface::class);
    }

    /**
     * @param \PHPUnit_Framework_TestCase $test
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Credential\X509CredentialInterface
     */
    public static function getX509CredentialMock(\PHPUnit_Framework_TestCase $test)
    {
        return $test->getMock(\LightSaml\Credential\X509CredentialInterface::class);
    }

    /**
     * @param \PHPUnit_Framework_TestCase $test
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    public static function getSessionMock(\PHPUnit_Framework_TestCase $test)
    {
        return $test->getMock(\Symfony\Component\HttpFoundation\Session\SessionInterface::class);
    }

    /**
     * @param \PHPUnit_Framework_TestCase $test
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\RobRichards\XMLSecLibs\XMLSecurityKey
     */
    public static function getXmlSecurityKeyMock(\PHPUnit_Framework_TestCase $test)
    {
        return $test->getMockBuilder(\RobRichards\XMLSecLibs\XMLSecurityKey::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }
}
