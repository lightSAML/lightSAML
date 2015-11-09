<?php

namespace LightSaml\Tests\Action\Profile\Outbound\Message;

use LightSaml\Action\Profile\Outbound\Message\MessageVersionAction;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\SamlConstants;
use LightSaml\Tests\TestHelper;

class MessageVersionActionTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_with_logger_and_version()
    {
        new MessageVersionAction(TestHelper::getLoggerMock($this), SamlConstants::VERSION_20);
    }

    public function test_sets_outbounding_message_version_to_value_from_constructor()
    {
        $action = new MessageVersionAction(TestHelper::getLoggerMock($this), $value = SamlConstants::VERSION_20);

        $context = TestHelper::getProfileContext();
        $context->getOutboundContext()->setMessage($message = new AuthnRequest());

        $action->execute($context);

        $this->assertEquals($value, $message->getVersion());
    }
}
