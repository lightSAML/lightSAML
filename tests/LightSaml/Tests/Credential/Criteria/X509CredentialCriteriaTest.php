<?php

namespace LightSaml\Tests\Credential\Criteria;

use LightSaml\Credential\Criteria\TrustCriteriaInterface;
use LightSaml\Credential\Criteria\X509CredentialCriteria;
use LightSaml\Tests\BaseTestCase;

class X509CredentialCriteriaTest extends BaseTestCase
{
    public function test_implements_trust_criteria_interface()
    {
        $this->assertInstanceOf(TrustCriteriaInterface::class, new X509CredentialCriteria());
    }
}
