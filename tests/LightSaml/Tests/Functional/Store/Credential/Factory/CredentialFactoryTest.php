<?php

namespace LightSaml\Tests\Functional\Store\Credential\Factory;

use LightSaml\Credential\KeyHelper;
use LightSaml\Credential\UsageType;
use LightSaml\Credential\X509Certificate;
use LightSaml\Credential\X509Credential;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Store\Credential\Factory\CredentialFactory;
use LightSaml\Store\EntityDescriptor\FixedEntityDescriptorStore;

class CredentialFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function test_creates_composite_store()
    {
        $factory = new CredentialFactory();

        $idpStore = new FixedEntityDescriptorStore();
        $idpStore->add(EntityDescriptor::load(__DIR__.'/../../../../../../../resources/sample/EntityDescriptor/idp-ed.xml'));

        $spStore = new FixedEntityDescriptorStore();
        $spStore->add(EntityDescriptor::load(__DIR__.'/../../../../../../../resources/sample/EntityDescriptor/sp-ed2.xml'));

        $ownCredential = new X509Credential(
            X509Certificate::fromFile(__DIR__.'/../../../../../../../resources/sample/Certificate/saml.crt'),
            KeyHelper::createPrivateKey(__DIR__.'/../../../../../../../resources/sample/Certificate/saml.pem', '', true)
        );
        $ownCredential->setEntityId('own');

        $extraCredential = new X509Credential(
            X509Certificate::fromFile(__DIR__.'/../../../../../../../resources/sample/Certificate/lightsaml-idp.crt'),
            KeyHelper::createPrivateKey(__DIR__.'/../../../../../../../resources/sample/Certificate/lightsaml-idp.key', '', true)
        );
        $extraCredential->setEntityId('extra');

        $store = $factory->build(
            $idpStore,
            $spStore,
            [$ownCredential],
            [$extraCredential]
        );

        /** @var X509Credential[] $credentials */
        $credentials = $store->getByEntityId('https://sts.windows.net/554fadfe-f04f-4975-90cb-ddc8b147aaa2/');
        $this->assertCount(1, $credentials);
        $this->assertEquals('https://sts.windows.net/554fadfe-f04f-4975-90cb-ddc8b147aaa2/', $credentials[0]->getEntityId());
        $this->assertEquals(['CN'=>'accounts.accesscontrol.windows.net'], $credentials[0]->getCertificate()->getSubject());
        $this->assertEquals(UsageType::SIGNING, $credentials[0]->getUsageType());

        $credentials = $store->getByEntityId('https://mt.evo.team/simplesaml/module.php/saml/sp/metadata.php/default-sp');
        $this->assertCount(2, $credentials);
        $this->assertEquals('https://mt.evo.team/simplesaml/module.php/saml/sp/metadata.php/default-sp', $credentials[0]->getEntityId());
        $subject = $credentials[0]->getCertificate()->getSubject();
        $this->assertEquals('mt.evo.team', $subject['CN']);
        $this->assertEquals(UsageType::SIGNING, $credentials[0]->getUsageType());
        $this->assertEquals(UsageType::ENCRYPTION, $credentials[1]->getUsageType());

        $credentials = $store->getByEntityId('own');
        $this->assertCount(1, $credentials);

        $credentials = $store->getByEntityId('extra');
        $this->assertCount(1, $credentials);
    }
}
