<?php

namespace LightSaml\Tests\Action\Profile\Outbound\Message;

use LightSaml\Action\Profile\Outbound\Message\MessageIdAction;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Tests\TestHelper;

class MessageIdActionTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_with_logger()
    {
        new MessageIdAction(TestHelper::getLoggerMock($this));
    }

    public function test_sets_id_of_outbounding_message()
    {
        $action = new MessageIdAction(TestHelper::getLoggerMock($this));

        $context = TestHelper::getProfileContext();
        $context->getOutboundContext()->setMessage($message = new AuthnRequest());

        $action->execute($context);

        $this->assertNotNull($message->getID());
    }
}
