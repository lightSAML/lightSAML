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
    public function test__profile_id_and_own_role_set_in_constructor()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);

        $this->assertEquals(Profiles::METADATA, $profileContext->getProfileId());
        $this->assertEquals(ProfileContext::ROLE_IDP, $profileContext->getOwnRole());
    }

    public function test_gets_set_relay_state()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $profileContext->setRelayState($expected = 'some-state');
        $this->assertEquals($expected, $profileContext->getRelayState());
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
    public function test__sub_context_creation($method, $expectedClass)
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $subContext = $profileContext->{$method}();
        $this->assertInstanceOf($expectedClass, $subContext);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlContextException
     * @expectedExceptionMessage Missing Request in HTTP request context
     */
    public function test__get_http_request_throws_on_empty_context()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $profileContext->getHttpRequest();
    }

    public function test__get_http_request_returns_from_context()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $profileContext->getHttpRequestContext()->setRequest($expectedValue = new Request());
        $this->assertSame($expectedValue, $profileContext->getHttpRequest());
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlContextException
     * @expectedExceptionMessage Missing message in inbound context
     */
    public function test__get_inbound_message_throws_on_empty_context()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $profileContext->getInboundMessage();
    }

    public function test__get_inbound_message_returns_from_context()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $profileContext->getInboundContext()->setMessage($expectedValue = $this->getMockForAbstractClass(SamlMessage::class));
        $this->assertSame($expectedValue, $profileContext->getInboundMessage());
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlContextException
     * @expectedExceptionMessage Missing message in outbound context
     */
    public function test__get_outbound_message_throws_on_empty_context()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $profileContext->getOutboundMessage();
    }

    public function test__get_outbound_message_returns_from_context()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $profileContext->getOutboundContext()->setMessage($expectedValue = $this->getMockForAbstractClass(SamlMessage::class));
        $this->assertSame($expectedValue, $profileContext->getOutboundMessage());
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlContextException
     * @expectedExceptionMessage Missing Endpoint in endpoint context
     */
    public function test__get_endpoint_throws_on_empty_context()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $profileContext->getEndpoint();
    }

    public function test__get_endpoint_returns_from_context()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $profileContext->getEndpointContext()->setEndpoint($expectedValue = $this->getMockForAbstractClass(Endpoint::class));
        $this->assertSame($expectedValue, $profileContext->getEndpoint());
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlContextException
     * @expectedExceptionMessage Missing EntityDescriptor in own entity context
     */
    public function test__get_own_entity_descriptor_throws_on_empty_context()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $profileContext->getOwnEntityDescriptor();
    }

    public function test__get_own_entity_descriptor_returns_from_context()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $profileContext->getOwnEntityContext()->setEntityDescriptor($expectedValue = new EntityDescriptor());
        $this->assertSame($expectedValue, $profileContext->getOwnEntityDescriptor());
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlContextException
     * @expectedExceptionMessage Missing EntityDescriptor in party entity context
     */
    public function test__get_party_entity_descriptor_throws_on_empty_context()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $profileContext->getPartyEntityDescriptor();
    }

    public function test__get_party_entity_descriptor_returns_from_context()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $profileContext->getPartyEntityContext()->setEntityDescriptor($expectedValue = new EntityDescriptor());
        $this->assertSame($expectedValue, $profileContext->getPartyEntityDescriptor());
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlContextException
     * @expectedExceptionMessage Missing TrustOptions in party entity context
     */
    public function test__get_trust_options_throws_on_empty_context()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $profileContext->getTrustOptions();
    }

    public function test__get_trust_options_returns_from_context()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $profileContext->getPartyEntityContext()->setTrustOptions($expectedValue = new TrustOptions());
        $this->assertSame($expectedValue, $profileContext->getTrustOptions());
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlContextException
     * @expectedExceptionMessage Missing SsoSessionState in logout context
     */
    public function test__get_logout_sso_session_state_throws_on_empty_context()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $profileContext->getLogoutSsoSessionState();
    }

    public function test__get_logout_sso_session_state_returns_from_context()
    {
        $profileContext = new ProfileContext(Profiles::METADATA, ProfileContext::ROLE_IDP);
        $profileContext->getLogoutContext()->setSsoSessionState($expectedValue = new SsoSessionState());
        $this->assertSame($expectedValue, $profileContext->getLogoutSsoSessionState());
    }
}
