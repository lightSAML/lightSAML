<?php

namespace LightSaml\Tests\Action\Profile\Outbound\Message;

use LightSaml\Action\Profile\Outbound\Message\MessageIssueInstantAction;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Tests\BaseTestCase;

class MessageIssueInstantActionTest extends BaseTestCase
{
    public function test_constructs_with_logger_and_time_provider()
    {
        new MessageIssueInstantAction($this->getLoggerMock(), $this->getTimeProviderMock());
        $this->assertTrue(true);
    }

    public function test_sets_outbounding_message_issue_instant_to_value_from_time_provider()
    {
        $action = new MessageIssueInstantAction(
            $this->getLoggerMock(),
            $timeProviderMock = $this->getTimeProviderMock()
        );

        $timeProviderMock->expects($this->any())
            ->method('getTimestamp')
            ->willReturn(1412399250);

        $context = $this->getProfileContext();
        $context->getOutboundContext()->setMessage($message = new AuthnRequest());

        $action->execute($context);

        $this->assertEquals('2014-10-04T05:07:30Z', $message->getIssueInstantString());
    }
}
