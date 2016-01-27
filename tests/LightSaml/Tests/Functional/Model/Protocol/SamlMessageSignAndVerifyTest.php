<?php

namespace LightSaml\Tests\Functional\Model\Protocol;

use LightSaml\Credential\KeyHelper;
use LightSaml\Credential\X509Certificate;
use LightSaml\Helper;
use LightSaml\Model\Assertion\Assertion;
use LightSaml\Model\Assertion\Issuer;
use LightSaml\Model\Assertion\NameID;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Model\Protocol\LogoutRequest;
use LightSaml\Model\Protocol\LogoutResponse;
use LightSaml\Model\Protocol\NameIDPolicy;
use LightSaml\Model\Protocol\Response;
use LightSaml\Model\Protocol\SamlMessage;
use LightSaml\Model\Protocol\Status;
use LightSaml\Model\Protocol\StatusCode;
use LightSaml\Model\XmlDSig\AbstractSignatureReader;
use LightSaml\Model\XmlDSig\SignatureWriter;
use LightSaml\SamlConstants;

class SamlMessageSignAndVerifyTest extends \PHPUnit_Framework_TestCase
{
    public function test_authn_request()
    {
        $authnRequest = new AuthnRequest();
        $authnRequest
            ->setAssertionConsumerServiceURL('https://mydomain.com/index.php?action_51=saml_callback')
            ->setNameIDPolicy($nameIdPolicy = new NameIDPolicy())
            ->setDestination('https://idp.com/login')
        ;
        $nameIdPolicy->setFormat(SamlConstants::NAME_ID_FORMAT_PERSISTENT);
        $nameIdPolicy->setAllowCreate(true);

        $this->verify($authnRequest);
    }

    public function test_logout_request()
    {
        $logoutRequest = new LogoutRequest();
        $logoutRequest
            ->setNameID(new NameID('user@domain.com', SamlConstants::NAME_ID_FORMAT_EMAIL))
        ;

        $this->verify($logoutRequest);
    }

    public function test_response()
    {
        $response = new Response();
        $response
            ->addAssertion($assertion = new Assertion())
            ->setStatus(new Status(new StatusCode(SamlConstants::STATUS_SUCCESS)))
        ;
        $assertion
            ->setId(Helper::generateID())
            ->setIssuer(new Issuer('https://issuer.com'))
        ;

        $this->verify($response);
    }

    public function test_logout_response()
    {
        $logoutResponse = new LogoutResponse();
        $logoutResponse->setStatus(new Status(new StatusCode(SamlConstants::STATUS_SUCCESS)));

        $this->verify($logoutResponse);
    }

    /**
     * @param SamlMessage $message
     */
    private function verify(SamlMessage $message)
    {
        $message
            ->setID(Helper::generateID())
            ->setIssueInstant(new \DateTime())
            ->setIssuer(new Issuer('https://mydomain.com'))
        ;
        $xml = $this->signAndSerialize($message);
        $this->deserializeAndVerify($xml, get_class($message));
    }

    /**
     * @param SamlMessage $message
     *
     * @return string
     */
    private function signAndSerialize(SamlMessage $message)
    {
        $signatureWriter = new SignatureWriter($this->getCertificate(), $this->getPrivateKey());
        $message->setSignature($signatureWriter);

        $serializationContext = new SerializationContext();
        $message->serialize($serializationContext->getDocument(), $serializationContext);

        $xml = $serializationContext->getDocument()->saveXML();

        return $xml;
    }

    /**
     * @param string $xml
     * @param string $class
     */
    private function deserializeAndVerify($xml, $class)
    {
        $deserializationContext = new DeserializationContext();
        $deserializationContext->getDocument()->loadXML($xml);

        /** @var SamlMessage $samlMessage */
        $samlMessage = new $class();
        $samlMessage->deserialize($deserializationContext->getDocument(), $deserializationContext);

        /** @var AbstractSignatureReader $signatureReader */
        $signatureReader = $samlMessage->getSignature();
        $ok = $signatureReader->validate(KeyHelper::createPublicKey($this->getCertificate()));

        $this->assertTrue($ok);
    }

    /**
     * @return X509Certificate
     */
    private function getCertificate()
    {
        return X509Certificate::fromFile(__DIR__.'/../../../../../../web/sp/saml.crt');
    }

    /**
     * @return \RobRichards\XMLSecLibs\XMLSecurityKey
     */
    private function getPrivateKey()
    {
        return KeyHelper::createPrivateKey(__DIR__.'/../../../../../../web/sp/saml.key', null, true);
    }
}
