<?php

namespace LightSaml\Tests\Functional\Binding;

use LightSaml\Binding\HttpRedirectBinding;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Context\Profile\MessageContext;
use LightSaml\Event\Events;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Model\XmlDSig\SignatureStringReader;
use LightSaml\Model\XmlDSig\SignatureWriter;
use LightSaml\Credential\KeyHelper;
use LightSaml\Credential\X509Certificate;
use LightSaml\Tests\BaseTestCase;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class HttpRedirectBindingFunctionalTest extends BaseTestCase
{
    public function test__send_authn_request()
    {
        $expectedRelayState = 'relayState';
        $expectedDestination = 'https://destination.com/auth';

        $request = $this->getAuthnRequest();
        $request->setRelayState($expectedRelayState);
        $request->setDestination($expectedDestination);

        $biding = new HttpRedirectBinding();

        $eventDispatcherMock = $this->getEventDispatcherMock();
        $eventDispatcherMock->expects($this->once())
            ->method('dispatch')
            ->willReturnCallback(function (GenericEvent $event, $name) {
                $this->assertEquals(Events::BINDING_MESSAGE_SENT, $name);
                $this->assertNotEmpty($event->getSubject());
                $doc = new \DOMDocument();
                $doc->loadXML($event->getSubject());
                $this->assertEquals('AuthnRequest', $doc->firstChild->localName);
                return $event;
            });

        $biding->setEventDispatcher($eventDispatcherMock);
        $this->assertSame($eventDispatcherMock, $biding->getEventDispatcher());

        $messageContext = new MessageContext();
        $messageContext->setMessage($request);

        /** @var RedirectResponse $response */
        $response = $biding->send($messageContext);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);

        $url = $response->getTargetUrl();
        $this->assertNotEmpty($url);

        $urlInfo = parse_url($url);

        $this->assertEquals($expectedDestination, $urlInfo['scheme'].'://'.$urlInfo['host'].$urlInfo['path']);

        $query = array();
        parse_str($urlInfo['query'], $query);

        $this->assertArrayHasKey('SAMLRequest', $query);
        $this->assertArrayHasKey('RelayState', $query);
        $this->assertArrayHasKey('SigAlg', $query);
        $this->assertArrayHasKey('Signature', $query);

        $this->assertEquals(
            'RY/NCsIwEITvPkXI3TaptY3BKkIvBb2oePAiMUmxYBPtbsXHdxFEGBgY5tuf5frd39nLD9DFUHGZCL5eTZabEW9h75+jB2TUCFDxcQg6GuhAB9N70Gj1YbPb6iwR+jFEjDbeOWvqil+Us7ZYqHlbuEU7IxfXq8vnReZblSvfzowvlVOlKzk7/XbTHMIBRt8EQBOQIiHzqZCko8y0EKQzZzUd1QWDX+qG+ACdpu4fJjb2qaEPeLqafAA=',
            $query['SAMLRequest']
        );
        $this->assertEquals($expectedRelayState, $query['RelayState']);
        $this->assertEquals('http://www.w3.org/2000/09/xmldsig#rsa-sha1', $query['SigAlg']);
        $this->assertEquals(
            'tm8dkiHro6oQkvleMAeAIWOLGKn116VVs/lRM+QpeR3YuKCjXcNFhI4xIunGYhfF+f2Li0GNdh6PqoyX3YVd7KVbm5hDTstJwx+PRYzMiBqwNMB5wCTtbZMiBiYbCT28ANU9ObWnYXbfKVNQJq/z8Uj2PFPXr+gVy30ttIXlHFKmGnYAwrlTEEYRDZ4clJ2tNEIxHZwwqHuPy1sd2xdWT8uKHJeRxTbvF2Vzw6ytzFeyQBIIPy/lk46czhi5a8uOb89y0XrDgSqHlwv2Vk/a5iWdYla235vWjAfuKSj6wD9Z0PnyNVPxlCl4B2bnRCWq1XBzYwsS12RYvd0vhO8DEA==',
            $query['Signature']
        );

        $xml = gzinflate(base64_decode($query['SAMLRequest']));

        $context = new DeserializationContext();
        $context->getDocument()->loadXML($xml);

        $receivedAuthnRequest = new AuthnRequest();
        $receivedAuthnRequest->deserialize($context->getDocument(), $context);

        $this->assertEquals($request->getID(), $receivedAuthnRequest->getID());
        $this->assertEquals($request->getIssueInstantTimestamp(), $receivedAuthnRequest->getIssueInstantTimestamp());
    }

    public function test__send_destination()
    {
        $expectedDestination = 'https://destination.com/auth';

        $request = $this->getAuthnRequest();

        $biding = new HttpRedirectBinding();

        $messageContext = new MessageContext();
        $messageContext->setMessage($request);

        /** @var RedirectResponse $response */
        $response = $biding->send($messageContext, $expectedDestination);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);

        $url = $response->getTargetUrl();
        $this->assertNotEmpty($url);

        $urlInfo = parse_url($url);

        $this->assertEquals($expectedDestination, $urlInfo['scheme'].'://'.$urlInfo['host'].$urlInfo['path']);
    }

    public function test__receive_authn_request()
    {
        $expectedRelayState = 'relayState';

        $binding = new HttpRedirectBinding();

        $eventDispatcherMock = $this->getEventDispatcherMock();
        $eventDispatcherMock->expects($this->once())
            ->method('dispatch')
            ->willReturnCallback(function (GenericEvent $event, $name) {
                $this->assertEquals(Events::BINDING_MESSAGE_RECEIVED, $name);
                $this->assertNotEmpty($event->getSubject());
                $doc = new \DOMDocument();
                $doc->loadXML($event->getSubject());
                $this->assertEquals('AuthnRequest', $doc->firstChild->localName);
                return $event;
            });

        $binding->setEventDispatcher($eventDispatcherMock);
        $this->assertSame($eventDispatcherMock, $binding->getEventDispatcher());

        $request = new Request();
        $request->server->add(array(
            'QUERY_STRING' => 'SAMLRequest='.urlencode('RY/NCsIwEITvPkXI3TaptY3BKkIvBb2oePAiMUmxYBPtbsXHdxFEGBgY5tuf5frd39nLD9DFUHGZCL5eTZabEW9h75+jB2TUCFDxcQg6GuhAB9N70Gj1YbPb6iwR+jFEjDbeOWvqil+Us7ZYqHlbuEU7IxfXq8vnReZblSvfzowvlVOlKzk7/XbTHMIBRt8EQBOQIiHzqZCko8y0EKQzZzUd1QWDX+qG+ACdpu4fJjb2qaEPeLqafAA=').
                                '&RelayState='.urlencode($expectedRelayState).
                                '&SigAlg='.urlencode('http://www.w3.org/2000/09/xmldsig#rsa-sha1').
                                '&Signature='.urlencode('SI4nZH+9tjLO24k2La/v5DJ/OfGWw/nKKc/Nh8ih/AN71HuIzFl30F3Va+pDOidRYgJ8dIB2Juf5DIQYggDz+AiR/NI9gkAIGKRYZ3bhBPzC0XVtTQ075Qxwa3HWimh2Lywj7WV0QANOptodnjp1aUf4SuSHfEYrcWTf5C0gOZhiXT7XIQH0wpL1BdLwaePlduVCfaaMq2iNadNFBHi2+d9+FrCHyxYdmR8r5CbNg1vNEHj1xYwWUMBEtvJIYAt116++ei78dQYKlv5Mz98pTB1bkjRtONh+w7Mdy1gGT+D/gDz1kl+kAfxIT6D2x54GFBKM01gAGRUrb0Z6j2Nn6Q=='),
        ));

        $messageContext = new MessageContext();
        $binding->receive($request, $messageContext);
        /** @var \LightSaml\Model\Protocol\AuthnRequest $message */
        $message = $messageContext->getMessage();

        $this->assertInstanceOf('LightSaml\Model\Protocol\AuthnRequest', $message);
        $this->assertEquals($expectedRelayState, $message->getRelayState());
        $this->assertEquals('_8dcc6985f6d9f385f0bbd4562ef848ef3ae78d87d7', $message->getID());
        $this->assertEquals('2014-01-01T12:00:00Z', $message->getIssueInstantString());
        $this->assertNotNull($message->getSignature());
        $this->assertInstanceOf('LightSaml\Model\XmlDSig\AbstractSignatureReader', $message->getSignature());
        $this->assertInstanceOf('LightSaml\Model\XmlDSig\SignatureStringReader', $message->getSignature());

        /** @var SignatureStringReader $signature */
        $signature = $message->getSignature();
        $this->assertEquals('http://www.w3.org/2000/09/xmldsig#rsa-sha1', $signature->getAlgorithm());
        $this->assertEquals(
            'SI4nZH+9tjLO24k2La/v5DJ/OfGWw/nKKc/Nh8ih/AN71HuIzFl30F3Va+pDOidRYgJ8dIB2Juf5DIQYggDz+AiR/NI9gkAIGKRYZ3bhBPzC0XVtTQ075Qxwa3HWimh2Lywj7WV0QANOptodnjp1aUf4SuSHfEYrcWTf5C0gOZhiXT7XIQH0wpL1BdLwaePlduVCfaaMq2iNadNFBHi2+d9+FrCHyxYdmR8r5CbNg1vNEHj1xYwWUMBEtvJIYAt116++ei78dQYKlv5Mz98pTB1bkjRtONh+w7Mdy1gGT+D/gDz1kl+kAfxIT6D2x54GFBKM01gAGRUrb0Z6j2Nn6Q==',
            $signature->getSignature()
        );
    }

    /**
     * @return AuthnRequest
     */
    private function getAuthnRequest()
    {
        $authnRequest = new AuthnRequest();
        $authnRequest->setIssueInstant('2014-01-01T12:00:00Z');
        $authnRequest->setID('_8dcc6985f6d9f385f0bbd4562ef848ef3ae78d87d7');

        $certificate = new X509Certificate();
        $certificate->loadFromFile(__DIR__.'/../../../../../resources/sample/Certificate/saml.crt');

        $key = KeyHelper::createPrivateKey(__DIR__.'/../../../../../resources/sample/Certificate/saml.pem', '', true);

        $authnRequest->setSignature(new SignatureWriter($certificate, $key));

        return $authnRequest;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private function getEventDispatcherMock()
    {
        return $this->getMockBuilder(\Symfony\Component\EventDispatcher\EventDispatcherInterface::class)->getMock();
    }
}
