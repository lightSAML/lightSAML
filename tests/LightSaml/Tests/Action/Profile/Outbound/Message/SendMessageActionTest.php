<?php

namespace LightSaml\Tests\Action\Profile\Outbound\Message;

use LightSaml\Action\Profile\Outbound\Message\SendMessageAction;
use LightSaml\Context\Profile\MessageContext;
use LightSaml\Model\Metadata\SingleSignOnService;
use LightSaml\SamlConstants;
use LightSaml\Tests\TestHelper;
use Symfony\Component\HttpFoundation\Response;

class SendMessageActionTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_with_logger_and_binding_factory()
    {
        new SendMessageAction(TestHelper::getLoggerMock($this), TestHelper::getBindingFactoryMock($this));
    }

    public function test_calls_binding_factory_with_endpoint_type_and_calls_binding_and_sets_response_to_context()
    {
        $action = new SendMessageAction(
            $loggerMock = TestHelper::getLoggerMock($this),
            $bindingFactoryMock = TestHelper::getBindingFactoryMock($this)
        );

        $context = TestHelper::getProfileContext();
        $context->getEndpointContext()->setEndpoint(new SingleSignOnService(
            $location = 'http://example/com',
            $bindingType = SamlConstants::BINDING_SAML2_HTTP_POST
        ));

        $bindingFactoryMock->expects($this->once())
            ->method('create')
            ->with($bindingType)
            ->willReturn($bindingMock = TestHelper::getBindingMock($this));

        $bindingMock->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(MessageContext::class))
            ->willReturn($response = new Response());

        $loggerMock->expects($this->once())
            ->method('info')
            ->with('Sending message', $this->isType('array'));

        $action->execute($context);

        $this->assertSame($response, $context->getHttpResponseContext()->getResponse());
    }
}
