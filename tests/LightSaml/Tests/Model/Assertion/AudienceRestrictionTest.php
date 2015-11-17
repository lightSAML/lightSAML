<?php

namespace LightSaml\Tests\Model\Assertion;

use LightSaml\Model\Assertion\AudienceRestriction;

class AudienceRestrictionTest extends \PHPUnit_Framework_TestCase
{
    public function test_has_audience()
    {
        $audienceRestriction = new AudienceRestriction(['a', 'b', 'c']);
        $this->assertTrue($audienceRestriction->hasAudience('a'));
        $this->assertFalse($audienceRestriction->hasAudience('x'));
    }
}
