<?php

namespace LightSaml\Tests;

use LightSaml\Context\Profile\ProfileContext;
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
}
