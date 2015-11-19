<?php

namespace LightSaml\Tests\Functional\Store\Credential;

use LightSaml\Credential\CredentialInterface;
use LightSaml\Store\Credential\X509FileCredentialStore;

class X509FileCredentialStoreTest extends \PHPUnit_Framework_TestCase
{
    public function test_returns_null_if_entity_id_does_not_match()
    {
        $store = new X509FileCredentialStore('foo', '', '', '');
        $this->assertCount(0, $store->getByEntityId('bar'));
    }

    public function test_returns_credential_with_loaded_specified_key_and_certificate()
    {
        $store = new X509FileCredentialStore(
            $entityId = 'foo',
            __DIR__.'/../../../../../../resources/sample/Certificate/saml.crt',
            __DIR__.'/../../../../../../resources/sample/Certificate/saml.pem',
            ''
        );
        $arr = $store->getByEntityId($entityId);

        $this->assertCount(1, $arr);
        $this->assertInstanceOf(CredentialInterface::class, $arr[0]);
        $this->assertEquals($entityId, $arr[0]->getEntityId());
        $this->assertNotNull($arr[0]->getPrivateKey());
        $this->assertNotNull($arr[0]->getPublicKey());
    }
}
