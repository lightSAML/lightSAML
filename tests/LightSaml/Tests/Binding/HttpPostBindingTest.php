<?php

namespace LightSaml\Tests\Binding;

use LightSaml\Binding\BindingFactory;
use LightSaml\Binding\HttpPostBinding;
use LightSaml\Context\Profile\MessageContext;
use LightSaml\Model\Protocol\Response;
use LightSaml\SamlConstants;
use LightSaml\Tests\BaseTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;

class HttpPostBindingTest extends BaseTestCase
{
    public function test_receive_throws_when_no_message()
    {
        $this->expectExceptionMessage("Missing SAMLRequest or SAMLResponse parameter");
        $this->expectException(\LightSaml\Error\LightSamlBindingException::class);
        $request = new Request();

        $binding = new HttpPostBinding();

        $messageContext = new MessageContext();

        $binding->receive($request, $messageContext);
    }

    public function test_relay_state_is_included_in_http_post()
    {
        $expectedRelayState = 'some_relay_state';

        $samlResponse = new Response();
        $samlResponse->setRelayState($expectedRelayState);

        $messageContext = new MessageContext();
        $messageContext->setMessage($samlResponse);

        $this->assertEquals($expectedRelayState, $messageContext->getMessage()->getRelayState());

        $bindingFactory = new BindingFactory();
        $binding = $bindingFactory->create(SamlConstants::BINDING_SAML2_HTTP_POST);

        $httpResponse = $binding->send($messageContext);

        $html = $httpResponse->getContent();

        $crawler = new Crawler($html);
        $relayStateInputs = $crawler->filter('body form input[name="RelayState"]');
        $this->assertEquals(1, $relayStateInputs->count());
        $actualRelayState = $relayStateInputs->first()->attr('value');
        $this->assertEquals($expectedRelayState, $actualRelayState);
    }
}
