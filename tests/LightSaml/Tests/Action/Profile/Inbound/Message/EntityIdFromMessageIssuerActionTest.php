<?php

namespace LightSaml\Tests\Action\Profile\Inbound\Message;

use LightSaml\Action\Profile\Inbound\Message\EntityIdFromMessageIssuerAction;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Assertion\Issuer;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Profile\Profiles;
use LightSaml\Tests\TestHelper;

class EntityIdFromMessageIssuerActionTest extends \PHPUnit_Framework_TestCase
{
    public function test_sets_inbound_message_issuer_entity_id_to_party_context()
    {
        $action = new EntityIdFromMessageIssuerAction(TestHelper::getLoggerMock($this));

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);

        $context->getInboundContext()->setMessage(new AuthnRequest());
        $context->getInboundMessage()->setIssuer(new Issuer($expectedEntityId = 'http://localhost/id'));

        $action->execute($context);

        $this->assertEquals($expectedEntityId, $context->getPartyEntityContext()->getEntityId());
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlContextException
     * @expectedExceptionMessage Inbound messages does not have Issuer
     */
    public function test_throws_when_inbound_message_has_no_issuer()
    {
        $action = new EntityIdFromMessageIssuerAction(TestHelper::getLoggerMock($this));

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);

        $context->getInboundContext()->setMessage(new AuthnRequest());

        $action->execute($context);
    }
}
