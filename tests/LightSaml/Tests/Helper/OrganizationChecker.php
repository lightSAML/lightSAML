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

use LightSaml\Model\Metadata\Organization;
use LightSaml\Tests\BaseTestCase;

class OrganizationChecker
{
    public static function check(BaseTestCase $test, $name, $display, $url, Organization $organization = null)
    {
        $test->assertNotNull($organization);
        $test->assertEquals($name, $organization->getOrganizationName());
        $test->assertEquals($display, $organization->getOrganizationDisplayName());
        $test->assertEquals($url, $organization->getOrganizationURL());
    }
}
