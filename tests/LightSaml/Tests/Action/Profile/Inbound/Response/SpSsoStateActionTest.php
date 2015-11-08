<?php

namespace LightSaml\Tests\Action\Profile\Inbound\Response;

use LightSaml\Action\Profile\Inbound\Response\SpSsoStateAction;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Assertion\Assertion;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Model\Protocol\Response;
use LightSaml\Profile\Profiles;
use LightSaml\Tests\TestHelper;

class SpSsoStateActionTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_with_logger_and_session_processor()
    {
        new SpSsoStateAction(TestHelper::getLoggerMock($this), $this->getSessionProcessorMock());
    }

    public function test_calls_session_processor()
    {
        $action = new SpSsoStateAction(TestHelper::getLoggerMock($this), $sessionProcessorMock = $this->getSessionProcessorMock());

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $context->getInboundContext()->setMessage($response = new Response());
        $response->addAssertion($assertion1 = new Assertion());
        $response->addAssertion($assertion2 = new Assertion());

        $context->getOwnEntityContext()->setEntityDescriptor(new EntityDescriptor($ownEntityId = 'http://own.entity.id'));
        $context->getPartyEntityContext()->setEntityDescriptor(new EntityDescriptor($partyEntityId = 'http://party.id'));

        $sessionProcessorMock->expects($this->once())
            ->method('processAssertions')
            ->with($this->isType('array'), $ownEntityId, $partyEntityId)
            ->willReturnCallback(function (array $assertions, $ownId, $partyId) use ($assertion1, $assertion2) {
                $this->assertSame($assertion1, $assertions[0]);
                $this->assertSame($assertion2, $assertions[1]);
            })
        ;

        $action->execute($context);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Resolver\Session\SessionProcessorInterface
     */
    private function getSessionProcessorMock()
    {
        return $this->getMock(\LightSaml\Resolver\Session\SessionProcessorInterface::class);
    }
}
