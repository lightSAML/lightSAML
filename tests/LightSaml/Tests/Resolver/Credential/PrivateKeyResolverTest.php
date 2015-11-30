<?php

namespace LightSaml\Tests\Resolver\Credential;

use LightSaml\Credential\CredentialInterface;
use LightSaml\Credential\Criteria\PrivateKeyCriteria;
use LightSaml\Criteria\CriteriaSet;
use LightSaml\Resolver\Credential\PrivateKeyResolver;
use RobRichards\XMLSecLibs\XMLSecurityKey;

class PrivateKeyResolverTest extends \PHPUnit_Framework_TestCase
{
    public function test__returns_only_credentials_with_private_keys_when_criteria_given()
    {
        $criteriaSet = new CriteriaSet([new PrivateKeyCriteria()]);

        $startingCredentials = [
            $firstCredential = $this->getMock(CredentialInterface::class),
            $secondCredential = $this->getMock(CredentialInterface::class),
            $thirdCredential = $this->getMock(CredentialInterface::class),
        ];

        $secondCredential->expects($this->any())
            ->method('getPrivateKey')
            ->willReturn($this->getXmlSecurityKeyMock());

        $resolver = new PrivateKeyResolver();

        $filteredCredentials = $resolver->resolve($criteriaSet, $startingCredentials);

        $this->assertCount(1, $filteredCredentials);
        $this->assertSame($secondCredential, $filteredCredentials[0]);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|XMLSecurityKey
     */
    private function getXmlSecurityKeyMock()
    {
        return $this->getMock(XMLSecurityKey::class, [], [], '', false);
    }
}
