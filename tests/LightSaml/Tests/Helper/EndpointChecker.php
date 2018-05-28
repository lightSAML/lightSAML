<?php

namespace LightSaml\Tests\Helper;

use LightSaml\Model\Metadata\Endpoint;
use LightSaml\Tests\BaseTestCase;

class EndpointChecker
{
    public static function check(BaseTestCase $test, $binding, $location, Endpoint $svc = null)
    {
        $test->assertNotNull($svc);
        $test->assertEquals($binding, $svc->getBinding());
        $test->assertEquals($location, $svc->getLocation());
    }
}
