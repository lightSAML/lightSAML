<?php

namespace LightSaml\Tests\Action\Profile\Outbound\Message;

use LightSaml\Action\Profile\Outbound\Message\CreateMessageIssuerAction;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Profile\Profiles;
use LightSaml\SamlConstants;
use LightSaml\Tests\BaseTestCase;

class CreateMessageIssuerActionTest extends BaseTestCase
{
    public function test_constructs_with_logger()
    {
        new CreateMessageIssuerAction($this->getLoggerMock());
        $this->assertTrue(true);
    }

    public function test_sets_own_entity_id_to_outbounding_message_issuer_with_name_id_format_entity()
    {
        $action = new CreateMessageIssuerAction($this->getLoggerMock());

        $context = new ProfileContext(Profiles::SSO_IDP_RECEIVE_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $context->getOutboundContext()->setMessage($message = new AuthnRequest());
        $context->getOwnEntityContext()->setEntityDescriptor(new EntityDescriptor($ownEntityId = 'http://own.entity.id'));

        $action->execute($context);

        $this->assertNotNull($message->getIssuer());
        $this->assertEquals($ownEntityId, $message->getIssuer()->getValue());
        $this->assertEquals(SamlConstants::NAME_ID_FORMAT_ENTITY, $message->getIssuer()->getFormat());
    }
}
