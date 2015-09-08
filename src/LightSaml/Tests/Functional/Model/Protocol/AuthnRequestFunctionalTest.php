<?php

namespace LightSaml\Tests\Functional\Model\Protocol;

use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\SamlConstants;

class AuthnRequestFunctionalTest extends \PHPUnit_Framework_TestCase
{
    public function testDeserializeRequest01()
    {
        $context = new DeserializationContext();
        $context->getDocument()->load(__DIR__.'/../../../../../../resources/sample/Request/request01.xml');

        $request = new AuthnRequest();
        $request->deserialize($context->getDocument()->firstChild, $context);

        $this->assertEquals('_8dcc6985f6d9f385f0bbd4562ef848ef3ae78d87d7', $request->getID());
        $this->assertEquals('2.0', $request->getVersion());
        $this->assertEquals('2013-10-10T15:26:20Z', $request->getIssueInstantString());
        $this->assertEquals('https://login.windows.net/554fadfe-f04f-4975-90cb-ddc8b147aaa2/saml2', $request->getDestination());
        $this->assertEquals('https://mt.evo.team/simplesaml/module.php/saml/sp/saml2-acs.php/default-sp', $request->getAssertionConsumerServiceURL());
        $this->assertEquals(SamlConstants::BINDING_SAML2_HTTP_POST, $request->getProtocolBinding());

        $this->assertNotNull($request->getIssuer());
        $this->assertEquals('https://mt.evo.team/simplesaml/module.php/saml/sp/metadata.php/default-sp', $request->getIssuer()->getValue());

        $this->assertNotNull($request->getNameIDPolicy());
        $this->assertEquals(SamlConstants::NAME_ID_FORMAT_PERSISTENT, $request->getNameIDPolicy()->getFormat());
        $this->assertTrue($request->getNameIDPolicy()->getAllowCreate());
    }
}
