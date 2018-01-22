<?php

namespace LightSaml\Tests\Action\Profile\Outbound;

use LightSaml\Action\Profile\Outbound\CreateLogoutRequestAction;
use LightSaml\Build\Container\PartyContainerInterface;
use LightSaml\Build\Container\StoreContainerInterface;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Model\Metadata\IdpSsoDescriptor;
use LightSaml\Model\Metadata\SingleLogoutService;
use LightSaml\Model\Protocol\LogoutRequest;
use LightSaml\Profile\Profiles;
use LightSaml\State\Sso\SsoSessionState;
use LightSaml\State\Sso\SsoState;
use LightSaml\Store\EntityDescriptor\EntityDescriptorStoreInterface;
use LightSaml\Store\Sso\SsoStateStoreInterface;
use LightSaml\Tests\TestHelper;

class CreateLogoutRequestActionTest extends \PHPUnit_Framework_TestCase
{
    public function test_creates_outbounding_logout_request()
    {
        $action = $this->createCreateLogoutRequestActionMock();
        $context = new ProfileContext(Profiles::SSO_SP_SEND_LOGOUT_REQUEST, ProfileContext::ROLE_SP);

        $action->execute($context);

        $this->assertInstanceOf(LogoutRequest::class, $context->getOutboundMessage());
    }

    /**
     * @expectedException        \LogicException
     * @expectedExceptionMessage No active session was found.
     */
    public function test_throws_logic_exception_if_not_logged_in()
    {
        $action = $this->createCreateLogoutRequestActionMockWithoutSession();
        $context = new ProfileContext(Profiles::SSO_SP_SEND_LOGOUT_REQUEST, ProfileContext::ROLE_SP);

        $action->execute($context);
    }

    public function createCreateLogoutRequestActionMock()
    {
        return new CreateLogoutRequestAction(TestHelper::getLoggerMock($this), $this->createPartyContainerMock(), $this->createStoreContainerMock([new SsoSessionState()]));
    }

    public function createCreateLogoutRequestActionMockWithoutSession()
    {
        return new CreateLogoutRequestAction(TestHelper::getLoggerMock($this), $this->createPartyContainerMock(), $this->createStoreContainerMock([]));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PartyContainerInterface
     */
    public function createPartyContainerMock()
    {
        $singleLogoutService = $this->getMock(SingleLogoutService::class);
        $singleLogoutService->expects($this->once())
            ->method('getLocation')
            ->willReturn('https://idp-url.com');

        $idpSsoDescriptor = $this->getMock(IdpSsoDescriptor::class);
        $idpSsoDescriptor->expects($this->once())
            ->method('getFirstSingleLogoutService')
            ->willReturn($singleLogoutService);

        $entityDescriptor = $this->getMock(EntityDescriptor::class);
        $entityDescriptor->expects($this->once())
            ->method('getFirstIdpSsoDescriptor')
            ->willReturn($idpSsoDescriptor);

        $idpEntityDescriptorStore = $this->getMock(EntityDescriptorStoreInterface::class);
        $idpEntityDescriptorStore->expects($this->once())
            ->method('get')
            ->willReturn($entityDescriptor);

        $partyContainer = $this->getMock(PartyContainerInterface::class);
        $partyContainer->expects($this->once())
            ->method('getIdpEntityDescriptorStore')
            ->willReturn($idpEntityDescriptorStore);

        return $partyContainer;
    }

    /**
     * @param array $sessions
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|StoreContainerInterface
     */
    public function createStoreContainerMock($sessions)
    {
        $ssoState = $this->getMock(SsoState::class);
        $ssoState->expects($this->once())
            ->method('getSsoSessions')
            ->willReturn($sessions);

        $ssoStateStore = $this->getMock(SsoStateStoreInterface::class);
        $ssoStateStore->expects($this->once())
            ->method('get')
            ->willReturn($ssoState);

        $storeContainer = $this->getMock(StoreContainerInterface::class);
        $storeContainer->expects($this->once())
            ->method('getSsoStateStore')
            ->willReturn($ssoStateStore);

        return $storeContainer;
    }
}
