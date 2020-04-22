<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
