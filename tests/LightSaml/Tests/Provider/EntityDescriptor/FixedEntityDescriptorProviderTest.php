<?php

namespace LightSaml\Tests\Provider\EntityDescriptor;

use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Provider\EntityDescriptor\FixedEntityDescriptorProvider;
use LightSaml\Tests\BaseTestCase;

class FixedEntityDescriptorProviderTest extends BaseTestCase
{
    public function test_returns_given_entity_descriptor()
    {
        $provider = new FixedEntityDescriptorProvider($expected = new EntityDescriptor());
        $this->assertSame($expected, $provider->get());
    }
}
