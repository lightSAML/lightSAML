<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests\Provider\Session;

use LightSaml\Provider\Session\FixedSessionInfoProvider;
use LightSaml\Tests\BaseTestCase;

class FixedSessionInfoProviderTest extends BaseTestCase
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
