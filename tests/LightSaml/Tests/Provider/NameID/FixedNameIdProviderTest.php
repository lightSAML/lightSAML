<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests\Provider\NameID;

use LightSaml\Model\Assertion\NameID;
use LightSaml\Provider\NameID\FixedNameIdProvider;
use LightSaml\Tests\BaseTestCase;

class FixedNameIdProviderTest extends BaseTestCase
{
    public function test_returns_given_name_id()
    {
        $provider = new FixedNameIdProvider($expected = new NameID());
        $this->assertSame($expected, $provider->getNameID($this->getProfileContext()));
    }
}
