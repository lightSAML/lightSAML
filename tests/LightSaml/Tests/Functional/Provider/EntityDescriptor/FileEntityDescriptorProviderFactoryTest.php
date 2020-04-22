<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests\Functional\Provider\EntityDescriptor;

use LightSaml\Provider\EntityDescriptor\FileEntityDescriptorProviderFactory;
use LightSaml\Tests\BaseTestCase;

class FileEntityDescriptorProviderFactoryTest extends BaseTestCase
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
