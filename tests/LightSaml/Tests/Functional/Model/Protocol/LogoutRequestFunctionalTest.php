<?php

namespace LightSaml\Tests\Functional\Model\Protocol;

use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Protocol\LogoutRequest;
use LightSaml\SamlConstants;

class LogoutRequestFunctionalTest extends \PHPUnit_Framework_TestCase
{
    public function test__deserialize_logout_request01()
    {
        $context = new DeserializationContext();
        $context->getDocument()->load(__DIR__.'/../../../../../../resources/sample/Request/logoutrequest01.xml');

        $request = new LogoutRequest();
        $request->deserialize($context->getDocument(), $context);

        $this->assertEquals('_6210989d671b429f1c82467626ffd0be990ded60bd', $request->getID());
        $this->assertEquals('2.0', $request->getVersion());
        $this->assertEquals('2013-11-07T16:07:25Z', $request->getIssueInstantString());
        $this->assertEquals('https://b1.bead.loc/adfs/ls/', $request->getDestination());
        $this->assertEquals('2013-11-07T16:07:25Z', $request->getNotOnOrAfterString());

        $this->assertNotNull($request->getIssuer());
        $this->assertEquals('https://mt.evo.team/simplesaml/module.php/saml/sp/metadata.php/default-sp', $request->getIssuer()->getValue());

        $this->assertNotNull($request->getNameID());
        $this->assertEquals('user', $request->getNameID()->getValue());
        $this->assertEquals(SamlConstants::NAME_ID_FORMAT_TRANSIENT, $request->getNameID()->getFormat());

        $this->assertEquals('_677952a2-7fb3-4e7a-b439-326366e677db', $request->getSessionIndex());
    }
}
