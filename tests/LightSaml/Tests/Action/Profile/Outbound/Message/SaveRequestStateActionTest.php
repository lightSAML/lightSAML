<?php

namespace LightSaml\Tests\Action\Profile\Outbound\Message;

use LightSaml\Action\Profile\Outbound\Message\SaveRequestStateAction;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\State\Request\RequestState;
use LightSaml\Tests\BaseTestCase;

class SaveRequestStateActionTest extends BaseTestCase
{
    public function test_constructs_with_logger_and_request_state_store()
    {
        new SaveRequestStateAction($this->getLoggerMock(), $this->getRequestStateStoreMock());
        $this->assertTrue(true);
    }

    public function test_creates_request_state_with_outbound_message_id()
    {
        $action = new SaveRequestStateAction(
            $this->getLoggerMock(),
            $requestStateStoreMock = $this->getRequestStateStoreMock()
        );

        $context = $this->getProfileContext();
        $context->getOutboundContext()->setMessage($message = new AuthnRequest());
        $message->setID($id = '123123123');

        $requestStateStoreMock->expects($this->once())
            ->method('set')
            ->with($this->isInstanceOf(RequestState::class))
            ->willReturnCallback(function (RequestState $requestState) use ($id) {
                $this->assertEquals($id, $requestState->getId());
            })
        ;

        $action->execute($context);
    }
}
