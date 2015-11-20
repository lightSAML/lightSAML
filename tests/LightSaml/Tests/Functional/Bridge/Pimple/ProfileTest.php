<?php

namespace LightSaml\Tests\Functional\Bridge\Pimple;

use LightSaml\Bridge\Pimple\Container\BuildContainer;
use LightSaml\Bridge\Pimple\Container\PartyContainer;
use Pimple\Container;

class ProfileTest extends \PHPUnit_Framework_TestCase
{
    const OWN_ENTITY_ID = 'http://own.id';

    public function test_idp_stores()
    {
        $buildContainer = $this->getBuildContainer();
        $allIdpEntityDescriptors = $buildContainer->getPartyContainer()->getIdpEntityDescriptorStore()->all();

        $this->assertCount(4, $allIdpEntityDescriptors);
        $this->assertEquals('https://idp.testshib.org/idp/shibboleth', $allIdpEntityDescriptors[0]->getEntityID());
        $this->assertEquals('https://sp.testshib.org/shibboleth-sp', $allIdpEntityDescriptors[1]->getEntityID());
        $this->assertEquals('http://localhost/lightSAML/lightSAML-IDP/web/idp', $allIdpEntityDescriptors[2]->getEntityID());
        $this->assertEquals('https://openidp.feide.no', $allIdpEntityDescriptors[3]->getEntityID());
    }

    public function test_metadata_profile()
    {
        $buildContainer = $this->getBuildContainer();

        $builder = new \LightSaml\Builder\Profile\Metadata\MetadataProfileBuilder($buildContainer);

        $context = $builder->buildContext();
        $action = $builder->buildAction();

        $action->execute($context);

        $this->assertNotNull($context->getHttpResponseContext()->getResponse());
        $xml = $context->getHttpResponseContext()->getResponse()->getContent();

        $root = new \SimpleXMLElement($xml);

        $this->assertEquals('EntityDescriptor', $root->getName());
        $this->assertEquals('SPSSODescriptor', $root->SPSSODescriptor->getName());
        $this->assertEquals('http://localhost/lightsaml/lightSAML/web/sp/acs.php', $root->SPSSODescriptor->AssertionConsumerService['Location']);
    }

    private function getBuildContainer()
    {
        $buildContainer = new BuildContainer($pimple = new Container());

        // OWN
        $ownCredential = new \LightSaml\Credential\X509Credential(
            \LightSaml\Credential\X509Certificate::fromFile(__DIR__.'/../../../../../../web/sp/saml.crt'),
            \LightSaml\Credential\KeyHelper::createPrivateKey(__DIR__.'/../../../../../../web/sp/saml.key', null, true)
        );
        $ownCredential->setEntityId(self::OWN_ENTITY_ID);

        $ownEntityDescriptor = new \LightSaml\Builder\EntityDescriptor\SimpleEntityDescriptorBuilder(
            self::OWN_ENTITY_ID,
            'http://localhost/lightsaml/lightSAML/web/sp/acs.php',
            null,
            $ownCredential->getCertificate()
        );

        $buildContainer->getPimple()->register(new \LightSaml\Bridge\Pimple\Container\Factory\OwnContainerProvider(
            $ownEntityDescriptor,
            [$ownCredential])
        );

        // SYSTEM
        $buildContainer->getPimple()->register(new \LightSaml\Bridge\Pimple\Container\Factory\SystemContainerProvider());

        // PARTY
        $buildContainer->getPimple()->register(new \LightSaml\Bridge\Pimple\Container\Factory\PartyContainerProvider());
        $pimple[PartyContainer::IDP_ENTITY_DESCRIPTOR] = function () {
            $idpProvider = new \LightSaml\Store\EntityDescriptor\FixedEntityDescriptorStore();
            $idpProvider->add(
                \LightSaml\Model\Metadata\EntitiesDescriptor::load(__DIR__.'/../../../../../../web/sp/testshib-providers.xml')
            );
            $idpProvider->add(
                \LightSaml\Model\Metadata\EntityDescriptor::load(__DIR__.'/../../../../../../web/sp/localhost-lightsaml-lightsaml-idp.xml')
            );
            $idpProvider->add(
                \LightSaml\Model\Metadata\EntityDescriptor::load(__DIR__.'/../../../../../../web/sp/openidp.feide.no.xml')
            );

            return $idpProvider;
        };

        // STORE
        $buildContainer->getPimple()->register(
            new \LightSaml\Bridge\Pimple\Container\Factory\StoreContainerProvider(
                $buildContainer->getSystemContainer()
            )
        );

        // PROVIDER
        $buildContainer->getPimple()->register(
            new \LightSaml\Bridge\Pimple\Container\Factory\ProviderContainerProvider()
        );

        // CREDENTIAL
        $buildContainer->getPimple()->register(
            new \LightSaml\Bridge\Pimple\Container\Factory\CredentialContainerProvider(
                $buildContainer->getPartyContainer(),
                $buildContainer->getOwnContainer()
            )
        );

        // SERVICE
        $buildContainer->getPimple()->register(
            new \LightSaml\Bridge\Pimple\Container\Factory\ServiceContainerProvider(
                $buildContainer->getCredentialContainer(),
                $buildContainer->getStoreContainer(),
                $buildContainer->getSystemContainer()
            )
        );

        return $buildContainer;
    }
}
