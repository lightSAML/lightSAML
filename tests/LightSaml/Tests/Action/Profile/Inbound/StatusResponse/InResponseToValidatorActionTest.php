<?php

namespace LightSaml\Tests\Action\Profile\Inbound\StatusResponse;

use LightSaml\Action\Profile\Inbound\StatusResponse\InResponseToValidatorAction;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Context\Profile\ProfileContexts;
use LightSaml\Context\Profile\RequestStateContext;
use LightSaml\Profile\Profiles;
use LightSaml\State\Request\RequestState;
use LightSaml\Tests\BaseTestCase;

class InResponseToValidatorActionTest extends BaseTestCase
{
    public function test_constructs_with_logger_and_request_state_store()
    {
        new InResponseToValidatorAction($this->getLoggerMock(), $this->getRequestStateStoreMock());
        $this->assertTrue(true);
    }

    public function test_does_nothing_if_no_in_response_to()
    {
        $action = new InResponseToValidatorAction(
            $loggerMock = $this->getLoggerMock(),
            $requestStateStoreMock = $this->getRequestStateStoreMock()
        );

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $context->getInboundContext()->setMessage($response = $this->getStatusResponseMock());

        $requestStateStoreMock->expects($this->never())
            ->method('get');

        $action->execute($context);
    }

    public function test_get_request_state_from_store_and_creates_context()
    {
        $action = new InResponseToValidatorAction(
            $loggerMock = $this->getLoggerMock(),
            $requestStateStoreMock = $this->getRequestStateStoreMock()
        );

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $context->getInboundContext()->setMessage($response = $this->getStatusResponseMock());
        $response->setInResponseTo($inResponseTo = '1234567890');

        $requestStateStoreMock->expects($this->once())
            ->method('get')
            ->with($inResponseTo)
            ->willReturn($requestState = new RequestState());

        $action->execute($context);

        /** @var RequestStateContext $requestStateContext */
        $requestStateContext = $context->getInboundContext()->getSubContext(ProfileContexts::REQUEST_STATE);
        $this->assertInstanceOf(RequestStateContext::class, $requestStateContext);
        $this->assertSame($requestState, $requestStateContext->getRequestState());
    }

    public function test_throws_context_exception_if_no_request_state_for_in_response_to_from_message()
    {
        $this->expectExceptionMessage("Unknown InResponseTo '1234567890'");
        $this->expectException(\LightSaml\Error\LightSamlContextException::class);
        $action = new InResponseToValidatorAction(
            $loggerMock = $this->getLoggerMock(),
            $requestStateStoreMock = $this->getRequestStateStoreMock()
        );

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $context->getInboundContext()->setMessage($response = $this->getStatusResponseMock());
        $response->setInResponseTo($inResponseTo = '1234567890');

        $requestStateStoreMock->expects($this->once())
            ->method('get')
            ->willReturn(null);

        $loggerMock->expects($this->once())
            ->method('critical');

        $action->execute($context);
    }

    /**
     * @param string $inResponseTo
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Model\Protocol\StatusResponse
     */
    private function getStatusResponseMock($inResponseTo = null)
    {
        $result = $this->getMockForAbstractClass(\LightSaml\Model\Protocol\StatusResponse::class);
        if ($inResponseTo) {
            $result->expects($this->any())
                ->method('getInResponseTo')
                ->willReturn($inResponseTo);
        }

        return $result;
    }
}
