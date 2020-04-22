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

use LightSaml\Credential\Criteria\AlgorithmCriteria;
use LightSaml\Credential\Criteria\TrustCriteriaInterface;
use LightSaml\Tests\BaseTestCase;
use RobRichards\XMLSecLibs\XMLSecurityKey;

class AlgorithmCriteriaTest extends BaseTestCase
{
    public function test_implements_trust_criteria_interface()
    {
        $this->assertInstanceOf(TrustCriteriaInterface::class, new AlgorithmCriteria(''));
    }

    public function test_returns_value_given_to_constructor()
    {
        $criteria = new AlgorithmCriteria($expectedValue = XMLSecurityKey::AES256_CBC);
        $this->assertEquals($expectedValue, $criteria->getAlgorithm());
    }
}
