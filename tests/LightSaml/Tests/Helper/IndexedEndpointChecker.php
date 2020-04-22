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
