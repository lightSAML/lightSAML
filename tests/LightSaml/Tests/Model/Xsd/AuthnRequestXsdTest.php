<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests\Model\Xsd;

use LightSaml\Helper;
use LightSaml\Model\Assertion\Issuer;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Model\Protocol\NameIDPolicy;
use LightSaml\SamlConstants;

class AuthnRequestXsdTest extends AbstractXsdValidationTest
{
    public function test_authn_request_with_xsd()
    {
        $authnRequest = new AuthnRequest();
        $authnRequest
            ->setAssertionConsumerServiceURL('https://sp.com/acs')
            ->setNameIDPolicy(new NameIDPolicy(SamlConstants::NAME_ID_FORMAT_EMAIL, true))
            ->setProtocolBinding(SamlConstants::PROTOCOL_SAML2)
            ->setID(Helper::generateID())
            ->setIssueInstant(new \DateTime())
            ->setDestination('https://idp.com/destination')
            ->setIssuer(new Issuer('https://sp.com'))
        ;
        $this->sign($authnRequest);

        $this->validateProtocol($authnRequest);
    }
}
