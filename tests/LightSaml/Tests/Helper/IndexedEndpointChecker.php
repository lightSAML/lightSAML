<?php

namespace LightSaml\Tests\Helper;

use LightSaml\Model\Metadata\IndexedEndpoint;
use LightSaml\Tests\BaseTestCase;

class IndexedEndpointChecker
{
    public static function check(BaseTestCase $test, $binding, $location, $index, $isDefault, IndexedEndpoint $svc = null)
    {
        EndpointChecker::check($test, $binding, $location, $svc);
        $test->assertEquals($index, $svc->getIndex());
        $test->assertEquals($isDefault, $svc->getIsDefaultBool());
    }
}
