<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
