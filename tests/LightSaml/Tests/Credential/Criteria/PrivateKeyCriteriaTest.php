<?php

namespace LightSaml\Tests\Credential\Criteria;

use LightSaml\Credential\Criteria\PrivateKeyCriteria;
use LightSaml\Credential\Criteria\TrustCriteriaInterface;

class PrivateKeyCriteriaTest extends \PHPUnit_Framework_TestCase
{
    public function test_implements_trust_criteria_interface()
    {
        $this->assertInstanceOf(TrustCriteriaInterface::class, new PrivateKeyCriteria());
    }
}
