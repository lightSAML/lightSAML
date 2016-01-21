<?php

namespace LightSaml\Tests\Model\Xsd;

use LightSaml\Helper;
use LightSaml\Model\Assertion\Issuer;
use LightSaml\Model\Protocol\LogoutResponse;
use LightSaml\Model\Protocol\Status;
use LightSaml\Model\Protocol\StatusCode;
use LightSaml\SamlConstants;

class LogoutResponseXsdTest extends AbstractXsdValidationTest
{
    public function test_logout_response_with_xsd()
    {
        $logoutResponse = new LogoutResponse();
        $logoutResponse
            ->setInResponseTo(Helper::generateID())
            ->setStatus(new Status(new StatusCode(SamlConstants::STATUS_SUCCESS), 'Successfully logged out from service'))
            ->setID(Helper::generateID())
            ->setIssueInstant(new \DateTime())
            ->setDestination('https://destination.com')
            ->setIssuer(new Issuer('https://issuer.com'))
        ;

        $this->sign($logoutResponse);
        $this->validateProtocol($logoutResponse);
    }
}
