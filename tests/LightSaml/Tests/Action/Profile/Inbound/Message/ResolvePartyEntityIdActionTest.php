<?php

namespace LightSaml\Tests\Action\Profile\Inbound\Message;

use LightSaml\Action\Profile\Inbound\Message\ResolvePartyEntityIdAction;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Meta\TrustOptions\TrustOptions;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Profile\Profiles;
use LightSaml\Store\TrustOptions\TrustOptionsStoreInterface;
use LightSaml\Tests\BaseTestCase;

class ResolvePartyEntityIdActionTest extends BaseTestCase
{
    public function test_constructs_with_logger_entity_descriptor_stores_and_trust_options_provider()
    {
        new ResolvePartyEntityIdAction(
            $this->getLoggerMock(),
            $this->getEntityDescriptorStoreMock(),
            $this->getEntityDescriptorStoreMock(),
            $this->getTrustOptionsStore()
        );
        $this->assertTrue(true);
    }

    public function test_does_nothing_if_party_entity_descriptor_and_trust_options_already_set_in_context()
    {
        $action = new ResolvePartyEntityIdAction(
            $logger = $this->getLoggerMock(),
            $spEntityStore = $this->getEntityDescriptorStoreMock(),
            $idpEntityStore = $this->getEntityDescriptorStoreMock(),
            $trustOptionsStore = $this->getTrustOptionsStore()
        );

        $context = new ProfileContext(Profiles::SSO_SP_SEND_AUTHN_REQUEST, ProfileContext::ROLE_SP);
        $context->getPartyEntityContext()->setEntityDescriptor(
            (new EntityDescriptor())->setEntityID($entityId = 'http://localhost/id')
        );
        $context->getPartyEntityContext()->setTrustOptions(new TrustOptions());

        $logger->expects($this->once())
            ->method('debug')
            ->with('Party EntityDescriptor and TrustOptions already set for "http://localhost/id"', $this->isType('array'))
        ;

        $action->execute($context);
    }

    public function test_throws_if_entity_id_is_not_set_in_context()
    {
        $this->expectExceptionMessage("EntityID is not set in the party context");
        $this->expectException(\LightSaml\Error\LightSamlContextException::class);
        $action = new ResolvePartyEntityIdAction(
            $logger = $this->getLoggerMock(),
            $spEntityStore = $this->getEntityDescriptorStoreMock(),
            $idpEntityStore = $this->getEntityDescriptorStoreMock(),
            $trustOptionsStore = $this->getTrustOptionsStore()
        );

        $context = new ProfileContext(Profiles::SSO_SP_SEND_AUTHN_REQUEST, ProfileContext::ROLE_SP);

        $logger->expects($this->once())
            ->method('critical')
            ->with('EntityID is not set in the party context', $this->isType('array'))
        ;

        $action->execute($context);
    }

    public function test_looks_for_idp_entity_descriptor_when_own_role_sp()
    {
        $action = new ResolvePartyEntityIdAction(
            $logger = $this->getLoggerMock(),
            $spEntityStore = $this->getEntityDescriptorStoreMock(),
            $idpEntityStore = $this->getEntityDescriptorStoreMock(),
            $trustOptionsStore = $this->getTrustOptionsStore()
        );

        $context = new ProfileContext(Profiles::SSO_SP_SEND_AUTHN_REQUEST, ProfileContext::ROLE_SP);
        $context->getPartyEntityContext()
            ->setEntityId($entityId = 'http://localhost/id')
            ->setTrustOptions(new TrustOptions());

        $idpEntityStore->expects($this->once())
            ->method('get')
            ->with($entityId)
            ->willReturn($entityDescriptor = (new EntityDescriptor())->setEntityID($entityId))
        ;
        $spEntityStore->expects($this->never())
            ->method('get')
        ;
        $logger->expects($this->once())
            ->method('debug')
            ->with('Known issuer resolved: "http://localhost/id"', $this->isType('array'))
        ;

        $action->execute($context);

        $this->assertSame($entityDescriptor, $context->getPartyEntityDescriptor());
    }

    public function test_looks_for_sp_entity_descriptor_when_own_role_idp()
    {
        $action = new ResolvePartyEntityIdAction(
            $logger = $this->getLoggerMock(),
            $spEntityStore = $this->getEntityDescriptorStoreMock(),
            $idpEntityStore = $this->getEntityDescriptorStoreMock(),
            $trustOptionsStore = $this->getTrustOptionsStore()
        );

        $context = new ProfileContext(Profiles::SSO_SP_SEND_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $context->getPartyEntityContext()
            ->setEntityId($entityId = 'http://localhost/id')
            ->setTrustOptions(new TrustOptions());

        $spEntityStore->expects($this->once())
            ->method('get')
            ->with($entityId)
            ->willReturn($entityDescriptor = (new EntityDescriptor())->setEntityID($entityId))
        ;
        $idpEntityStore->expects($this->never())
            ->method('get')
        ;
        $logger->expects($this->once())
            ->method('debug')
            ->with('Known issuer resolved: "http://localhost/id"', $this->isType('array'))
        ;

        $action->execute($context);

        $this->assertSame($entityDescriptor, $context->getPartyEntityDescriptor());
        $this->assertSame($entityDescriptor, $context->getPartyEntityContext()->getEntityDescriptor());
    }

    public function test_looks_for_trust_options()
    {
        $action = new ResolvePartyEntityIdAction(
            $logger = $this->getLoggerMock(),
            $spEntityStore = $this->getEntityDescriptorStoreMock(),
            $idpEntityStore = $this->getEntityDescriptorStoreMock(),
            $trustOptionsStore = $this->getTrustOptionsStore()
        );

        $context = new ProfileContext(Profiles::SSO_SP_SEND_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $context->getPartyEntityContext()
            ->setEntityDescriptor($entityDescriptor = (new EntityDescriptor())->setEntityID($entityId = 'http://localhost/id'));

        $spEntityStore->expects($this->never())->method('get');

        $idpEntityStore->expects($this->never())->method('get');

        $trustOptionsStore->expects($this->once())
            ->method('get')
            ->with($entityId)
            ->willReturn($trustOptions = new TrustOptions())
        ;

        $action->execute($context);

        $this->assertSame($trustOptions, $context->getPartyEntityContext()->getTrustOptions());
        $this->assertSame($entityDescriptor, $context->getPartyEntityContext()->getEntityDescriptor());
    }

    public function test_creates_default_trust_options_if_none_resolved()
    {
        $action = new ResolvePartyEntityIdAction(
            $logger = $this->getLoggerMock(),
            $spEntityStore = $this->getEntityDescriptorStoreMock(),
            $idpEntityStore = $this->getEntityDescriptorStoreMock(),
            $trustOptionsStore = $this->getTrustOptionsStore()
        );

        $context = new ProfileContext(Profiles::SSO_SP_SEND_AUTHN_REQUEST, ProfileContext::ROLE_IDP);
        $context->getPartyEntityContext()
            ->setEntityDescriptor($entityDescriptor = (new EntityDescriptor())->setEntityID($entityId = 'http://localhost/id'));

        $spEntityStore->expects($this->never())->method('get');

        $idpEntityStore->expects($this->never())->method('get');

        $trustOptionsStore->expects($this->once())
            ->method('get')
            ->with($entityId)
            ->willReturn(null)
        ;

        $action->execute($context);

        $this->assertNotNull($context->getPartyEntityContext()->getTrustOptions());
        $this->assertNotNull($context->getPartyEntityContext()->getEntityDescriptor());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Store\TrustOptions\TrustOptionsStoreInterface
     */
    private function getTrustOptionsStore()
    {
        return $this->getMockBuilder(TrustOptionsStoreInterface::class)->getMock();
    }
}
