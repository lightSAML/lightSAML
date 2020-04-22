<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
