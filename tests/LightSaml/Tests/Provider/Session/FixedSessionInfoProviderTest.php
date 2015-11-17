<?php

namespace LightSaml\Tests\Provider\Session;

use LightSaml\Provider\Session\FixedSessionInfoProvider;

class FixedSessionInfoProviderTest extends \PHPUnit_Framework_TestCase
{
    public function test_returns_given_values()
    {
        $provider = new FixedSessionInfoProvider(
            $authnInstant = 123123123,
            $sessionIndex = '11111',
            $authnContextClassRef = 'aaaaa'
        );

        $this->assertEquals($authnInstant, $provider->getAuthnInstant());
        $this->assertEquals($sessionIndex, $provider->getSessionIndex());
        $this->assertEquals($authnContextClassRef, $provider->getAuthnContextClassRef());
    }
}
