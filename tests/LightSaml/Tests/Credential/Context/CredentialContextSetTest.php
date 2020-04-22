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

use LightSaml\Credential\Context\CredentialContextSet;
use LightSaml\Credential\Context\MetadataCredentialContext;
use LightSaml\Tests\BaseTestCase;

class CredentialContextSetTest extends BaseTestCase
{
    public function test_metadata_context_is_null_upon_creation()
    {
        $context = new CredentialContextSet();

        $this->assertNull($context->get(MetadataCredentialContext::class));
    }

    public function test_returns_set_metadata_context()
    {
        $context = new CredentialContextSet([$metadataContextMock = $this->getMetadataContextMock()]);

        $this->assertSame($metadataContextMock, $context->get(MetadataCredentialContext::class));
    }

    public function test_returns_all_contexts()
    {
        $context = new CredentialContextSet($expected = [$this->getMetadataContextMock(), $this->getMetadataContextMock()]);

        $all = $context->all();
        $this->assertCount(2, $all);

        $this->assertSame($expected[0], $all[0]);
        $this->assertSame($expected[1], $all[1]);
    }

    public function test_throws_invalid_argument_exception_if_constructed_with_non_credential_context_array()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected CredentialContextInterface');

        new CredentialContextSet([new \stdClass()]);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\LightSaml\Credential\Context\MetadataCredentialContext
     */
    private function getMetadataContextMock()
    {
        return $this->getMockBuilder(\LightSaml\Credential\Context\MetadataCredentialContext::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
