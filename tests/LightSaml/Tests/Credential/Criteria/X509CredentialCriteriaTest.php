<?php

namespace LightSaml\Tests\Credential\Criteria;

use LightSaml\Credential\Criteria\TrustCriteriaInterface;
use LightSaml\Credential\Criteria\X509CredentialCriteria;

class X509CredentialCriteriaTest extends \PHPUnit_Framework_TestCase
{
    public function test_implements_trust_criteria_interface()
    {
        $this->assertInstanceOf(TrustCriteriaInterface::class, new X509CredentialCriteria());
    }
}
