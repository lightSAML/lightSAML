<?php

namespace LightSaml\Tests\Provider\NameID;

use LightSaml\Model\Assertion\NameID;
use LightSaml\Provider\NameID\FixedNameIdProvider;
use LightSaml\Tests\TestHelper;

class FixedNameIdProviderTest extends \PHPUnit_Framework_TestCase
{
    public function test_returns_given_name_id()
    {
        $provider = new FixedNameIdProvider($expected = new NameID());
        $this->assertSame($expected, $provider->getNameID(TestHelper::getProfileContext()));
    }
}
