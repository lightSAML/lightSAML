<?php

namespace LightSaml\Tests\Helper;

use LightSaml\Model\Metadata\Endpoint;

class EndpointChecker
{
    public static function check(\PHPUnit_Framework_TestCase $test, $binding, $location, Endpoint $svc = null)
    {
        $test->assertNotNull($svc);
        $test->assertEquals($binding, $svc->getBinding());
        $test->assertEquals($location, $svc->getLocation());
    }
}
