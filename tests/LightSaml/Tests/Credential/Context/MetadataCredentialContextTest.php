<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests\Credential\Context;

use LightSaml\Credential\Context\MetadataCredentialContext;
use LightSaml\Tests\BaseTestCase;

class MetadataCredentialContextTest extends BaseTestCase
{
    public function test_returns_objects_set_on_construct()
    {
        $context = new MetadataCredentialContext(
            $keyDescriptor = $this->getKeyDescriptorMock(),
            $roleDescriptor = $this->getRoleDescriptorMock(),
            $entityDescriptor = $this->getEntityDescriptorMock()
        );

        $this->assertSame($keyDescriptor, $context->getKeyDescriptor());
        $this->assertSame($roleDescriptor, $context->getRoleDescriptor());
        $this->assertSame($entityDescriptor, $context->getEntityDescriptor());
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\LightSaml\Model\Metadata\KeyDescriptor
     */
    private function getKeyDescriptorMock()
    {
        return $this->getMockBuilder(\LightSaml\Model\Metadata\KeyDescriptor::class)->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\LightSaml\Model\Metadata\RoleDescriptor
     */
    private function getRoleDescriptorMock()
    {
        return $this->getMockBuilder(\LightSaml\Model\Metadata\RoleDescriptor::class)->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\LightSaml\Model\Metadata\EntityDescriptor
     */
    private function getEntityDescriptorMock()
    {
        return $this->getMockBuilder(\LightSaml\Model\Metadata\EntityDescriptor::class)->getMock();
    }
}
