<?php

namespace LightSaml\Tests\Resolver\Credential;

use LightSaml\Credential\CredentialInterface;
use LightSaml\Criteria\CriteriaSet;
use LightSaml\Resolver\Credential\CredentialResolverInterface;
use LightSaml\Resolver\Credential\CredentialResolverQuery;
use LightSaml\Tests\BaseTestCase;
use RobRichards\XMLSecLibs\XMLSecurityKey;

class CredentialResolverQueryTest extends BaseTestCase
{
    public function test__extends_criteria_set()
    {
        $reflectionClass = new \ReflectionClass(CredentialResolverQuery::class);
        $this->assertEquals(CriteriaSet::class, $reflectionClass->getParentClass()->name);
    }

    public function test__instantiates_with_credential_resolver()
    {
        new CredentialResolverQuery($this->getMockBuilder(CredentialResolverInterface::class)->getMock());
        $this->assertTrue(true);
    }

    public function test__resolve_calls_credential_resolver_and_stores_returned_credentials()
    {
        $credentialResolverMock = $this->getMockBuilder(CredentialResolverInterface::class)->getMock();
        $query = new CredentialResolverQuery($credentialResolverMock);
        $credentialResolverMock
            ->expects($this->once())
            ->method('resolve')
            ->with($query)
            ->willReturn($expectedCredentials = [1, 2, 3])
        ;
        $query->resolve();

        $this->assertEquals($expectedCredentials, $query->allCredentials());
    }

    public function test__first_credential()
    {
        $credentialResolverMock = $this->getMockBuilder(CredentialResolverInterface::class)->getMock();
        $query = new CredentialResolverQuery($credentialResolverMock);
        $credentialResolverMock
            ->expects($this->once())
            ->method('resolve')
            ->with($query)
            ->willReturn($expectedCredentials = [
                $firstCredential = $this->getMockBuilder(CredentialInterface::class)->getMock(),
                $secondCredential = $this->getMockBuilder(CredentialInterface::class)->getMock(),
                $thirdCredential = $this->getMockBuilder(CredentialInterface::class)->getMock(),
            ])
        ;
        $query->resolve();

        $this->assertSame($firstCredential, $query->firstCredential());
    }

    public function test__public_keys()
    {
        $credentialResolverMock = $this->getMockBuilder(CredentialResolverInterface::class)->getMock();
        $query = new CredentialResolverQuery($credentialResolverMock);
        $credentialResolverMock
            ->expects($this->once())
            ->method('resolve')
            ->with($query)
            ->willReturn($expectedCredentials = [
                $firstCredential = $this->getMockBuilder(CredentialInterface::class)->getMock(),
                $secondCredential = $this->getMockBuilder(CredentialInterface::class)->getMock(),
                $thirdCredential = $this->getMockBuilder(CredentialInterface::class)->getMock(),
            ])
        ;

        $firstCredential->expects($this->any())
            ->method('getPublicKey')
            ->willReturn($this->getXmlSecurityKeyMock());
        $thirdCredential->expects($this->any())
            ->method('getPublicKey')
            ->willReturn($this->getXmlSecurityKeyMock());
        $query->resolve();

        $publicKeys = $query->getPublicKeys();

        $this->assertCount(2, $publicKeys);
        $this->assertSame($firstCredential, $publicKeys[0]);
        $this->assertSame($thirdCredential, $publicKeys[1]);

        $this->assertCount(0, $query->getPrivateKeys());
    }

    public function test__private_keys()
    {
        $credentialResolverMock = $this->getMockBuilder(CredentialResolverInterface::class)->getMock();
        $query = new CredentialResolverQuery($credentialResolverMock);
        $credentialResolverMock
            ->expects($this->once())
            ->method('resolve')
            ->with($query)
            ->willReturn($expectedCredentials = [
                $firstCredential = $this->getMockBuilder(CredentialInterface::class)->getMock(),
                $secondCredential = $this->getMockBuilder(CredentialInterface::class)->getMock(),
                $thirdCredential = $this->getMockBuilder(CredentialInterface::class)->getMock(),
            ])
        ;

        $secondCredential->expects($this->any())
            ->method('getPrivateKey')
            ->willReturn($this->getXmlSecurityKeyMock());
        $thirdCredential->expects($this->any())
            ->method('getPrivateKey')
            ->willReturn($this->getXmlSecurityKeyMock());
        $query->resolve();

        $privateKeys = $query->getPrivateKeys();

        $this->assertCount(2, $privateKeys);
        $this->assertSame($secondCredential, $privateKeys[0]);
        $this->assertSame($thirdCredential, $privateKeys[1]);

        $this->assertCount(0, $query->getPublicKeys());
    }
}
