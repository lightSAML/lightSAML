<?php

namespace LightSaml\Tests\Action\Assertion\Inbound;

use LightSaml\Action\Assertion\Inbound\KnownAssertionIssuerAction;
use LightSaml\Model\Assertion\Assertion;
use LightSaml\Model\Assertion\Issuer;
use LightSaml\Tests\TestHelper;

class KnownAssertionIssuerActionTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_with_logger_and_entity_descriptor_store()
    {
        new KnownAssertionIssuerAction(TestHelper::getLoggerMock($this), TestHelper::getEntityDescriptorStoreMock($this));
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlContextException
     * @expectedExceptionMessage Assertion element must have an issuer element
     */
    public function test_throws_context_exception_when_assertion_has_no_issuer()
    {
        $action = new KnownAssertionIssuerAction(
            $loggerMock = TestHelper::getLoggerMock($this),
            $entityDescriptorStoreMock = TestHelper::getEntityDescriptorStoreMock($this)
        );

        $context = TestHelper::getAssertionContext($assertion = new Assertion());

        $loggerMock->expects($this->once())
            ->method('error')
            ->with('Assertion element must have an issuer element');

        $action->execute($context);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlContextException
     * @expectedExceptionMessage Unknown issuer 'http://issuer.com'
     */
    public function test_throws_context_exception_on_unknown_issuer()
    {
        $action = new KnownAssertionIssuerAction(
            $loggerMock = TestHelper::getLoggerMock($this),
            $entityDescriptorStoreMock = TestHelper::getEntityDescriptorStoreMock($this)
        );

        $context = TestHelper::getAssertionContext($assertion = new Assertion());
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
            $loggerMock = TestHelper::getLoggerMock($this),
            $entityDescriptorStoreMock = TestHelper::getEntityDescriptorStoreMock($this)
        );

        $context = TestHelper::getAssertionContext($assertion = new Assertion());
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
