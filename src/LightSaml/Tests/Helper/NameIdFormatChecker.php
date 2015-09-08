<?php

namespace LightSaml\Tests\Helper;

use LightSaml\Model\Metadata\SSODescriptor;

class NameIdFormatChecker
{
    public static function check(\PHPUnit_Framework_TestCase $test, SSODescriptor $descriptor, array $expectedNameIdFormats)
    {
        $test->assertCount(count($expectedNameIdFormats), $descriptor->getAllNameIDFormats());
        foreach ($expectedNameIdFormats as $nameIdFormat) {
            $test->assertTrue($descriptor->hasNameIDFormat($nameIdFormat));
        }
    }
}
