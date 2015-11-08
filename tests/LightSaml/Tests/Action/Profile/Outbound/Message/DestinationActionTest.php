<?php

namespace LightSaml\Tests\Action\Profile\Outbound\Message;

use LightSaml\Action\Profile\Outbound\Message\DestinationAction;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Metadata\SingleSignOnService;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Profile\Profiles;
use LightSaml\Tests\TestHelper;

class DestinationActionTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_with_logger()
    {
        new DestinationAction(TestHelper::getLoggerMock($this));
    }

    public function test_sets_outbounding_message_destination_to_endpoint_context_value()
    {
        $action = new DestinationAction(TestHelper::getLoggerMock($this));

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $context->getOutboundContext()->setMessage($message = new AuthnRequest());

        $context->getEndpointContext()->setEndpoint($endpoint = new SingleSignOnService());
        $endpoint->setLocation($location = 'http://idp.com/login');

        $action->execute($context);

        $this->assertEquals($location, $message->getDestination());
    }
}
