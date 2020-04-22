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
