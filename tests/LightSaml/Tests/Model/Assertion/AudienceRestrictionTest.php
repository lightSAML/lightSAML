<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests\Model\Assertion;

use LightSaml\Model\Assertion\AudienceRestriction;
use LightSaml\Tests\BaseTestCase;

class AudienceRestrictionTest extends BaseTestCase
{
    public function test_has_audience()
    {
        $audienceRestriction = new AudienceRestriction(['a', 'b', 'c']);
        $this->assertTrue($audienceRestriction->hasAudience('a'));
        $this->assertFalse($audienceRestriction->hasAudience('x'));
    }
}
