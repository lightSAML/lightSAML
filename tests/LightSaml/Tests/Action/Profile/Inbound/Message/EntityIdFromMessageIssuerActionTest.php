<?php

namespace LightSaml\Tests\Action\Profile\Inbound\Message;

use LightSaml\Action\Profile\Inbound\Message\EntityIdFromMessageIssuerAction;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Assertion\Issuer;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Profile\Profiles;
use LightSaml\Tests\BaseTestCase;

class EntityIdFromMessageIssuerActionTest extends BaseTestCase
{
    public function test_sets_inbound_message_issuer_entity_id_to_party_context()
    {
        $action = new EntityIdFromMessageIssuerAction($this->getLoggerMock());

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);

        $context->getInboundContext()->setMessage(new AuthnRequest());
        $context->getInboundMessage()->setIssuer(new Issuer($expectedEntityId = 'http://localhost/id'));

        $action->execute($context);

        $this->assertEquals($expectedEntityId, $context->getPartyEntityContext()->getEntityId());
    }

    public function test_throws_when_inbound_message_has_no_issuer()
    {
        $this->expectExceptionMessage("Inbound messages does not have Issuer");
        $this->expectException(\LightSaml\Error\LightSamlContextException::class);
        $action = new EntityIdFromMessageIssuerAction($this->getLoggerMock());

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);

        $context->getInboundContext()->setMessage(new AuthnRequest());

        $action->execute($context);
    }
}
