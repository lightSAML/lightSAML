<?php

namespace LightSaml\Tests\Action\Profile\Outbound\Message;

use LightSaml\Action\Profile\Outbound\Message\MessageIdAction;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Tests\BaseTestCase;

class MessageIdActionTest extends BaseTestCase
{
    public function test_constructs_with_logger()
    {
        new MessageIdAction($this->getLoggerMock());
        $this->assertTrue(true);
    }

    public function test_sets_id_of_outbounding_message()
    {
        $action = new MessageIdAction($this->getLoggerMock());

        $context = $this->getProfileContext();
        $context->getOutboundContext()->setMessage($message = new AuthnRequest());

        $action->execute($context);

        $this->assertNotNull($message->getID());
    }
}
