<?php

namespace LightSaml\Tests\Functional\Provider\EntityDescriptor;

use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Provider\EntitiesDescriptor\FileEntitiesDescriptorProvider;
use LightSaml\Provider\EntityDescriptor\EntitiesDescriptorEntityProvider;

class EntitiesDescriptorEntityProviderTest extends \PHPUnit_Framework_TestCase
{
    public function test___provides_by_specified_entity_id()
    {
        $entitiesProvider = new FileEntitiesDescriptorProvider(
            __DIR__.'/../../../../../../resources/sample/EntitiesDescriptor/testshib-providers.xml'
        );

        $provider = new EntitiesDescriptorEntityProvider(
            $entitiesProvider,
            $expectedEntityId = 'https://idp.testshib.org/idp/shibboleth'
        );
        $entityDescriptor = $provider->get();
        $this->assertInstanceOf(EntityDescriptor::class, $entityDescriptor);
        $this->assertEquals($expectedEntityId, $entityDescriptor->getEntityID());

        $provider = new EntitiesDescriptorEntityProvider(
            $entitiesProvider,
            $expectedEntityId = 'https://sp.testshib.org/shibboleth-sp'
        );
        $entityDescriptor = $provider->get();
        $this->assertInstanceOf(EntityDescriptor::class, $entityDescriptor);
        $this->assertEquals($expectedEntityId, $entityDescriptor->getEntityID());
    }
}
