<?php

namespace LightSaml\Tests\Credential\Context;

use LightSaml\Credential\Context\MetadataCredentialContext;

class MetadataCredentialContextTest extends \PHPUnit_Framework_TestCase
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
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Model\Metadata\KeyDescriptor
     */
    private function getKeyDescriptorMock()
    {
        return $this->getMock('LightSaml\Model\Metadata\KeyDescriptor');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Model\Metadata\RoleDescriptor
     */
    private function getRoleDescriptorMock()
    {
        return $this->getMock('LightSaml\Model\Metadata\RoleDescriptor');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Model\Metadata\EntityDescriptor
     */
    private function getEntityDescriptorMock()
    {
        return $this->getMock('LightSaml\Model\Metadata\EntityDescriptor');
    }
}
