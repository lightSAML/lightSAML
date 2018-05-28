<?php

namespace LightSaml\Tests\Helper;

use LightSaml\Model\Metadata\SSODescriptor;
use LightSaml\Tests\BaseTestCase;

class NameIdFormatChecker
{
    public static function check(BaseTestCase $test, SSODescriptor $descriptor, array $expectedNameIdFormats)
    {
        $test->assertCount(count($expectedNameIdFormats), $descriptor->getAllNameIDFormats());
        foreach ($expectedNameIdFormats as $nameIdFormat) {
            $test->assertTrue($descriptor->hasNameIDFormat($nameIdFormat));
        }
    }
}
