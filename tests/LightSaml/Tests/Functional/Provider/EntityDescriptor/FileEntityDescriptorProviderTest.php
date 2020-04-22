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

use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Provider\EntityDescriptor\FileEntityDescriptorProvider;
use LightSaml\Tests\BaseTestCase;

class FileEntityDescriptorProviderTest extends BaseTestCase
{
    public function test_loads_from_file()
    {
        $provider = new FileEntityDescriptorProvider(
            __DIR__.'/../../../../../../resources/sample/EntityDescriptor/idp-ed.xml'
        );

        $entityDescriptor = $provider->get();

        $this->assertInstanceOf(EntityDescriptor::class, $entityDescriptor);
        $this->assertEquals(
            'https://sts.windows.net/554fadfe-f04f-4975-90cb-ddc8b147aaa2/',
            $entityDescriptor->getEntityID()
        );
    }
}
