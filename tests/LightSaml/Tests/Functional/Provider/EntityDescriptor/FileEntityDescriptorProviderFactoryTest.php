<?php

namespace LightSaml\Tests\Functional\Provider\EntityDescriptor;

use LightSaml\Provider\EntityDescriptor\FileEntityDescriptorProviderFactory;

class FileEntityDescriptorProviderFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function test_loads_entity_descriptor_from_file()
    {
        $provider = FileEntityDescriptorProviderFactory::fromEntityDescriptorFile(
            __DIR__.'/../../../../../../resources/sample/EntityDescriptor/idp-ed.xml'
        );

        $entityDescriptor = $provider->get();

        $this->assertEquals('_127800fe-39ac-46ad-b073-6fb6106797a0', $entityDescriptor->getID());
    }

    public function test_loads_entities_descriptor_from_file()
    {
        $provider = FileEntityDescriptorProviderFactory::fromEntitiesDescriptorFile(
            __DIR__.'/../../../../../../resources/sample/EntitiesDescriptor/testshib-providers.xml',
            'https://idp.testshib.org/idp/shibboleth'
        );

        $entityDescriptor = $provider->get();

        $this->assertEquals('https://idp.testshib.org/idp/shibboleth', $entityDescriptor->getEntityID());
    }
}
