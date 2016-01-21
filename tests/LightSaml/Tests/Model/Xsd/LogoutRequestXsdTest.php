<?php

namespace LightSaml\Tests\Model\Xsd;

use LightSaml\Helper;
use LightSaml\Model\Assertion\Issuer;
use LightSaml\Model\Assertion\NameID;
use LightSaml\Model\Protocol\LogoutRequest;
use LightSaml\SamlConstants;

class LogoutRequestXsdTest extends AbstractXsdValidationTest
{
    public function test_logout_request_with_xsd()
    {
        $logoutRequest = new LogoutRequest();
        $logoutRequest
            ->setNameID(new NameID('foo@domain.com', SamlConstants::NAME_ID_FORMAT_EMAIL))
            ->setSessionIndex(Helper::generateID())
            ->setNotOnOrAfter(new \DateTime('+2 minute'))
            ->setID(Helper::generateID())
            ->setIssueInstant(new \DateTime())
            ->setDestination('https://destination.com')
            ->setIssuer(new Issuer('https://issuer.com'))
        ;

        $this->sign($logoutRequest);
        $this->validateProtocol($logoutRequest);
    }
}
