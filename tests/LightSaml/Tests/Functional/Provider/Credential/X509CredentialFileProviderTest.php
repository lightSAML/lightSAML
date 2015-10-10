<?php

namespace LightSaml\Tests\Functional\Provider\Credential;

use LightSaml\Credential\X509CredentialInterface;
use LightSaml\Provider\Credential\CredentialProviderInterface;
use LightSaml\Provider\Credential\X509CredentialFileProvider;

class X509CredentialFileProviderTest extends \PHPUnit_Framework_TestCase
{
    public function test___implements_credential_provider_interface()
    {
        $reflection = new \ReflectionClass(X509CredentialFileProvider::class);
        $this->assertTrue($reflection->implementsInterface(CredentialProviderInterface::class));
    }

    public function test___loads_specified_files()
    {
        $provider = new X509CredentialFileProvider(
            $expectedEntityId = 'http://localhost',
            __DIR__.'/../../../../../../resources/sample/Certificate/saml.crt',
            __DIR__.'/../../../../../../resources/sample/Certificate/saml.pem',
            null
        );

        $credential = $provider->get();

        $this->assertInstanceOf(X509CredentialInterface::class, $credential);
        $this->assertEquals($expectedEntityId, $credential->getEntityId());
        $this->assertNotNull($credential->getCertificate());
        $this->assertNotNull($credential->getPrivateKey());
    }
}
