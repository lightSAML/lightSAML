<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests\Store\EntityDescriptor;

use LightSaml\Model\Metadata\EntitiesDescriptor;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Store\EntityDescriptor\FixedEntityDescriptorStore;
use LightSaml\Tests\BaseTestCase;

class FixedEntityDescriptorStoreTest extends BaseTestCase
{
    public function test_entity_descriptor_can_be_added()
    {
        $store = new FixedEntityDescriptorStore();
        $store->add($expected = new EntityDescriptor($entityId = 'http://entity.com'));

        $this->assertTrue($store->has($entityId));
        $this->assertSame($expected, $store->get($entityId));
    }

    public function test_entities_descriptor_can_be_added()
    {
        $entitiesDescriptor = new EntitiesDescriptor();
        $entitiesDescriptor->addItem(new EntityDescriptor('http://some.com'));
        $entitiesDescriptor->addItem($expected = new EntityDescriptor($entityId = 'http://entity.com'));
        $entitiesDescriptor->addItem(new EntityDescriptor('http://third.com'));

        $store = new FixedEntityDescriptorStore();
        $store->add($entitiesDescriptor);

        $this->assertTrue($store->has($entityId));
        $this->assertSame($expected, $store->get($entityId));
    }

    public function test_all_returns_all_added()
    {
        $entitiesDescriptor = new EntitiesDescriptor();
        $entitiesDescriptor->addItem(new EntityDescriptor('http://some.com'));
        $entitiesDescriptor->addItem(new EntityDescriptor($entityId = 'http://entity.com'));
        $entitiesDescriptor->addItem(new EntityDescriptor('http://third.com'));

        $store = new FixedEntityDescriptorStore();
        $store->add($entitiesDescriptor);

        $this->assertCount(3, $entitiesDescriptor->getAllItems());
    }
}
