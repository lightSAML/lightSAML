<?php

namespace LightSaml\Tests\Context\Profile;

use LightSaml\Context\Profile\EndpointContext;
use LightSaml\Context\Profile\EntityContext;
use LightSaml\Context\Profile\HttpRequestContext;
use LightSaml\Context\Profile\HttpResponseContext;
use LightSaml\Context\Profile\LogoutContext;
use LightSaml\Context\Profile\MessageContext;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Meta\TrustOptions\TrustOptions;
use LightSaml\Model\Metadata\Endpoint;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Model\Protocol\SamlMessage;
use LightSaml\Profile\Profiles;
use LightSaml\State\Sso\SsoSessionState;
use Symfony\Component\HttpFoundation\Request;

class ProfileContextTest extends \PHPUnit_Framework_TestCase
{
    public function testProfileIdAndOwnRoleSetInConstructor()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);

        $this->assertEquals(Profiles::METADATA, $profileContext->getProfileId());
        $this->assertEquals(ProfileContext::ROLE_IDP, $profileContext->getOwnRole());
    }

    public function subContextCreationProvider()
    {
        return [
            ['getInboundContext', MessageContext::class],
            ['getOutboundContext', MessageContext::class],
            ['getHttpRequestContext', HttpRequestContext::class],
            ['getHttpResponseContext', HttpResponseContext::class],
            ['getOwnEntityContext', EntityContext::class],
            ['getPartyEntityContext', EntityContext::class],
            ['getEndpointContext', EndpointContext::class],
            ['getLogoutContext', LogoutContext::class],
        ];
    }
    /**
     * @dataProvider subContextCreationProvider
     */
    public function testSubContextCreation($method, $expectedClass)
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $subContext = $profileContext->{$method}();
        $this->assertInstanceOf($expectedClass, $subContext);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlContextException
     * @expectedExceptionMessage Missing Request in HTTP request context
     */
    public function testGetHttpRequestThrowsOnEmptyContext()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $profileContext->getHttpRequest();
    }

    public function testGetHttpRequestReturnsFromContext()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $profileContext->getHttpRequestContext()->setRequest($expectedValue = new Request());
        $this->assertSame($expectedValue, $profileContext->getHttpRequest());
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlContextException
     * @expectedExceptionMessage Missing message in inbound context
     */
    public function testGetInboundMessageThrowsOnEmptyContext()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $profileContext->getInboundMessage();
    }

    public function testGetInboundMessageReturnsFromContext()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $profileContext->getInboundContext()->setMessage($expectedValue = $this->getMockForAbstractClass(SamlMessage::class));
        $this->assertSame($expectedValue, $profileContext->getInboundMessage());
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlContextException
     * @expectedExceptionMessage Missing message in outbound context
     */
    public function testGetOutboundMessageThrowsOnEmptyContext()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $profileContext->getOutboundMessage();
    }

    public function testGetOutboundMessageReturnsFromContext()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $profileContext->getOutboundContext()->setMessage($expectedValue = $this->getMockForAbstractClass(SamlMessage::class));
        $this->assertSame($expectedValue, $profileContext->getOutboundMessage());
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlContextException
     * @expectedExceptionMessage Missing Endpoint in endpoint context
     */
    public function testGetEndpointThrowsOnEmptyContext()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $profileContext->getEndpoint();
    }

    public function testGetEndpointReturnsFromContext()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $profileContext->getEndpointContext()->setEndpoint($expectedValue = $this->getMockForAbstractClass(Endpoint::class));
        $this->assertSame($expectedValue, $profileContext->getEndpoint());
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlContextException
     * @expectedExceptionMessage Missing EntityDescriptor in own entity context
     */
    public function testGetOwnEntityDescriptorThrowsOnEmptyContext()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $profileContext->getOwnEntityDescriptor();
    }

    public function testGetOwnEntityDescriptorReturnsFromContext()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $profileContext->getOwnEntityContext()->setEntityDescriptor($expectedValue = new EntityDescriptor());
        $this->assertSame($expectedValue, $profileContext->getOwnEntityDescriptor());
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlContextException
     * @expectedExceptionMessage Missing EntityDescriptor in party entity context
     */
    public function testGetPartyEntityDescriptorThrowsOnEmptyContext()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $profileContext->getPartyEntityDescriptor();
    }

    public function testGetPartyEntityDescriptorReturnsFromContext()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $profileContext->getPartyEntityContext()->setEntityDescriptor($expectedValue = new EntityDescriptor());
        $this->assertSame($expectedValue, $profileContext->getPartyEntityDescriptor());
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlContextException
     * @expectedExceptionMessage Missing TrustOptions in party entity context
     */
    public function testGetTrustOptionsThrowsOnEmptyContext()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $profileContext->getTrustOptions();
    }

    public function testGetTrustOptionsReturnsFromContext()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $profileContext->getPartyEntityContext()->setTrustOptions($expectedValue = new TrustOptions());
        $this->assertSame($expectedValue, $profileContext->getTrustOptions());
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlContextException
     * @expectedExceptionMessage Missing SsoSessionState in logout context
     */
    public function testGetLogoutSsoSessionStateThrowsOnEmptyContext()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $profileContext->getLogoutSsoSessionState();
    }

    public function testGetLogoutSsoSessionStateReturnsFromContext()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $profileContext->getLogoutContext()->setSsoSessionState($expectedValue = new SsoSessionState());
        $this->assertSame($expectedValue, $profileContext->getLogoutSsoSessionState());
    }
}
