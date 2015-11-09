<?php

namespace LightSaml\Tests\Action\Profile\Outbound\Message;

use LightSaml\Action\Profile\Outbound\Message\MessageIssueInstantAction;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Tests\TestHelper;

class MessageIssueInstantActionTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_with_logger_and_time_provider()
    {
        new MessageIssueInstantAction(TestHelper::getLoggerMock($this), TestHelper::getTimeProviderMock($this));
    }

    public function test_sets_outbounding_message_issue_instant_to_value_from_time_provider()
    {
        $action = new MessageIssueInstantAction(
            TestHelper::getLoggerMock($this),
            $timeProviderMock = TestHelper::getTimeProviderMock($this)
        );

        $timeProviderMock->expects($this->any())
            ->method('getTimestamp')
            ->willReturn(1412399250);

        $context = TestHelper::getProfileContext();
        $context->getOutboundContext()->setMessage($message = new AuthnRequest());

        $action->execute($context);

        $this->assertEquals('2014-10-04T05:07:30Z', $message->getIssueInstantString());
    }
}
