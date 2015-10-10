<?php

namespace LightSaml\Tests\Credential\Context;

use LightSaml\Credential\Context\CredentialContextSet;
use LightSaml\Credential\Context\MetadataCredentialContext;

class CredentialContextSetTest extends \PHPUnit_Framework_TestCase
{
    public function testMetadataContextIsNullUponCreation()
    {
        $context = new CredentialContextSet();

        $this->assertNull($context->get(MetadataCredentialContext::class));
    }

    public function testReturnsSetMetadataContext()
    {
        $context = new CredentialContextSet([$metadataContextMock = $this->getMetadataContextMock()]);

        $this->assertSame($metadataContextMock, $context->get(MetadataCredentialContext::class));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Credential\Context\MetadataCredentialContext
     */
    private function getMetadataContextMock()
    {
        return $this->getMock('LightSaml\Credential\Context\MetadataCredentialContext', array(), array(), '', false);
    }
}
