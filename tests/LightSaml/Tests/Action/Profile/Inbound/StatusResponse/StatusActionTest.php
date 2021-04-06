<?php

namespace LightSaml\Tests\Action\Profile\Inbound\StatusResponse;

use LightSaml\Action\Profile\Inbound\StatusResponse\StatusAction;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Protocol\Response;
use LightSaml\Model\Protocol\Status;
use LightSaml\Model\Protocol\StatusCode;
use LightSaml\Profile\Profiles;
use LightSaml\SamlConstants;
use LightSaml\Tests\BaseTestCase;

class StatusActionTest extends BaseTestCase
{
    public function test_constructs_with_logger()
    {
        new StatusAction($this->getLoggerMock());
        $this->assertTrue(true);
    }

    public function test_does_nothing_if_status_success()
    {
        $action = new StatusAction($this->getLoggerMock());

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $context->getInboundContext()->setMessage($response = new Response());
        $response->setStatus(new Status(new StatusCode(SamlConstants::STATUS_SUCCESS)));

        $action->execute($context);

        $this->assertTrue(true);
    }

    public function test_throws_context_exception_if_no_status()
    {
        $this->expectExceptionMessage("Status response does not have Status set");
        $this->expectException(\LightSaml\Error\LightSamlContextException::class);
        $action = new StatusAction($loggerMock = $this->getLoggerMock());

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $context->getInboundContext()->setMessage($response = new Response());

        $loggerMock->expects($this->once())
            ->method('error');

        $action->execute($context);
    }

    public function test_throws_authentication_exception_if_status_not_success()
    {
        $this->expectExceptionMessage("Unsuccessful SAML response: urn:oasis:names:tc:SAML:2.0:status:Requester\n\nurn:oasis:names:tc:SAML:2.0:status:UnsupportedBinding");
        $this->expectException(\LightSaml\Error\LightSamlAuthenticationException::class);
        $action = new StatusAction($loggerMock = $this->getLoggerMock());

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $context->getInboundContext()->setMessage($response = new Response());
        $response->setStatus(new Status($statusCode = new StatusCode(SamlConstants::STATUS_REQUESTER)));
        $statusCode->setStatusCode(new StatusCode(SamlConstants::STATUS_UNSUPPORTED_BINDING));

        $loggerMock->expects($this->once())
            ->method('error');

        $action->execute($context);
    }
}
