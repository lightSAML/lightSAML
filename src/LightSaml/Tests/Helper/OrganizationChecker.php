<?php

namespace LightSaml\Tests\Helper;

use LightSaml\Model\Metadata\Organization;

class OrganizationChecker
{
    public static function check(\PHPUnit_Framework_TestCase $test, $name, $display, $url, Organization $organization = null)
    {
        $test->assertNotNull($organization);
        $test->assertEquals($name, $organization->getOrganizationName());
        $test->assertEquals($display, $organization->getOrganizationDisplayName());
        $test->assertEquals($url, $organization->getOrganizationURL());
    }
}
