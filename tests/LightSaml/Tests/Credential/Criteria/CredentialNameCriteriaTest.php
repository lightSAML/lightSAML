<?php

namespace LightSaml\Tests\Credential\Criteria;

use LightSaml\Credential\Criteria\CredentialNameCriteria;
use LightSaml\Credential\Criteria\TrustCriteriaInterface;
use LightSaml\Tests\BaseTestCase;

class CredentialNameCriteriaTest extends BaseTestCase
{
    public function test_implements_trust_criteria_interface()
    {
        $this->assertInstanceOf(TrustCriteriaInterface::class, new CredentialNameCriteria(''));
    }

    public function test_returns_value_given_to_constructor()
    {
        $criteria = new CredentialNameCriteria($expectedValue = 'abc');
        $this->assertEquals($expectedValue, $criteria->getName());
    }
}
