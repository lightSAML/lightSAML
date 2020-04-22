<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests\Resolver\Credential;

use LightSaml\Credential\CredentialInterface;
use LightSaml\Credential\Criteria\PrivateKeyCriteria;
use LightSaml\Criteria\CriteriaSet;
use LightSaml\Resolver\Credential\PrivateKeyResolver;
use LightSaml\Tests\BaseTestCase;

class PrivateKeyResolverTest extends BaseTestCase
{
    public function test__returns_only_credentials_with_private_keys_when_criteria_given()
    {
        $criteriaSet = new CriteriaSet([new PrivateKeyCriteria()]);

        $startingCredentials = [
            $firstCredential = $this->getMockBuilder(CredentialInterface::class)->getMock(),
            $secondCredential = $this->getMockBuilder(CredentialInterface::class)->getMock(),
            $thirdCredential = $this->getMockBuilder(CredentialInterface::class)->getMock(),
        ];

        $secondCredential->expects($this->any())
            ->method('getPrivateKey')
            ->willReturn($this->getXmlSecurityKeyMock());

        $resolver = new PrivateKeyResolver();

        $filteredCredentials = $resolver->resolve($criteriaSet, $startingCredentials);

        $this->assertCount(1, $filteredCredentials);
        $this->assertSame($secondCredential, $filteredCredentials[0]);
    }
}
