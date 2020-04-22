<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests\Action\Assertion\Inbound;

use LightSaml\Action\Assertion\Inbound\KnownAssertionIssuerAction;
use LightSaml\Model\Assertion\Assertion;
use LightSaml\Model\Assertion\Issuer;
use LightSaml\Tests\BaseTestCase;

class KnownAssertionIssuerActionTest extends BaseTestCase
{
    public function test_constructs_with_logger_and_entity_descriptor_store()
    {
        new KnownAssertionIssuerAction($this->getLoggerMock(), $this->getEntityDescriptorStoreMock());
        $this->assertTrue(true);
    }

    public function test_throws_context_exception_when_assertion_has_no_issuer()
    {
        $this->expectException(\LightSaml\Error\LightSamlContextException::class);
        $this->expectExceptionMessage('Assertion element must have an issuer element');

        $action = new KnownAssertionIssuerAction(
            $loggerMock = $this->getLoggerMock(),
            $entityDescriptorStoreMock = $this->getEntityDescriptorStoreMock()
        );

        $context = $this->getAssertionContext($assertion = new Assertion());

        $loggerMock->expects($this->once())
            ->method('error')
            ->with('Assertion element must have an issuer element');

        $action->execute($context);
    }

    public function test_throws_context_exception_on_unknown_issuer()
    {
        $this->expectException(\LightSaml\Error\LightSamlContextException::class);
        $this->expectExceptionMessage('Unknown issuer \'http://issuer.com\'');

        $action = new KnownAssertionIssuerAction(
            $loggerMock = $this->getLoggerMock(),
            $entityDescriptorStoreMock = $this->getEntityDescriptorStoreMock()
        );

        $context = $this->getAssertionContext($assertion = new Assertion());
        $assertion->setIssuer(new Issuer($issuer = 'http://issuer.com'));

        $entityDescriptorStoreMock->expects($this->once())
            ->method('has')
            ->with($issuer)
            ->willReturn(false);

        $loggerMock->expects($this->once())
            ->method('error')
            ->with("Unknown issuer 'http://issuer.com'");

        $action->execute($context);
    }

    public function test_logs_known_issuer()
    {
        $action = new KnownAssertionIssuerAction(
            $loggerMock = $this->getLoggerMock(),
            $entityDescriptorStoreMock = $this->getEntityDescriptorStoreMock()
        );

        $context = $this->getAssertionContext($assertion = new Assertion());
        $assertion->setIssuer(new Issuer($issuer = 'http://issuer.com'));

        $entityDescriptorStoreMock->expects($this->once())
            ->method('has')
            ->with($issuer)
            ->willReturn(true);

        $loggerMock->expects($this->once())
            ->method('debug')
            ->with('Known assertion issuer: "http://issuer.com"');

        $action->execute($context);
    }
}
