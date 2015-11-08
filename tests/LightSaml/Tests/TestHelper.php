<?php

namespace LightSaml\Tests;

use LightSaml\Context\Profile\ProfileContext;
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
