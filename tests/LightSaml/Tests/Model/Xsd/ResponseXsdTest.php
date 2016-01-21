<?php

namespace LightSaml\Tests\Model\Xsd;

use LightSaml\ClaimTypes;
use LightSaml\Helper;
use LightSaml\Model\Assertion\Assertion;
use LightSaml\Model\Assertion\Attribute;
use LightSaml\Model\Assertion\AttributeStatement;
use LightSaml\Model\Assertion\AudienceRestriction;
use LightSaml\Model\Assertion\AuthnContext;
use LightSaml\Model\Assertion\AuthnStatement;
use LightSaml\Model\Assertion\Conditions;
use LightSaml\Model\Assertion\Issuer;
use LightSaml\Model\Assertion\NameID;
use LightSaml\Model\Assertion\Subject;
use LightSaml\Model\Assertion\SubjectConfirmation;
use LightSaml\Model\Assertion\SubjectConfirmationData;
use LightSaml\Model\Protocol\Response;
use LightSaml\Model\Protocol\Status;
use LightSaml\Model\Protocol\StatusCode;
use LightSaml\SamlConstants;

class ResponseXsdTest extends AbstractXsdValidationTest
{
    public function test_fail_response_with_xsd()
    {
        $response = new Response();
        $response
            ->setStatus(new Status(
                (new StatusCode(SamlConstants::STATUS_REQUESTER))->setStatusCode(new StatusCode(SamlConstants::STATUS_UNSUPPORTED_BINDING)),
                'ACS75006: An error occurred while processing a SAML2 Authentication request. ACS75003: SAML protocol response cannot be sent via bindings other than HTTP POST. Requested binding: urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect'
            ))
            ->setInResponseTo(Helper::generateID())
            ->setID(Helper::generateID())
            ->setIssueInstant(new \DateTime())
            ->setIssuer(new Issuer('https://idp.com'))
        ;
        $this->sign($response);

        $this->validateProtocol($response);
    }

    public function test_success_response_with_xsd()
    {
        $response = new Response();
        $response
            ->setStatus(new Status(
                (new StatusCode(SamlConstants::STATUS_SUCCESS))
            ))
            ->setInResponseTo(Helper::generateID())
            ->setID(Helper::generateID())
            ->setIssueInstant(new \DateTime())
            ->setIssuer(new Issuer('https://idp.com'))
        ;

        $response->addAssertion($assertion = new Assertion());
        $assertion
            ->setId(Helper::generateID())
            ->setIssueInstant(new \DateTime())
            ->setIssuer(new Issuer('https://idp.com'))
            ->setSubject((new Subject())
                ->setNameID(new NameID('foo@idp.com', SamlConstants::NAME_ID_FORMAT_EMAIL))
                ->addSubjectConfirmation((new SubjectConfirmation())
                    ->setMethod(SamlConstants::CONFIRMATION_METHOD_BEARER)
                    ->setSubjectConfirmationData((new SubjectConfirmationData())
                        ->setInResponseTo(Helper::generateID())
                        ->setNotOnOrAfter(new \DateTime('+1 hour'))
                        ->setRecipient('https://sp.com/acs')
                    )
                )
            )
            ->setConditions((new Conditions())
                ->setNotBefore(new \DateTime())
                ->setNotOnOrAfter(new \DateTime('+1 hour'))
                ->addItem((new AudienceRestriction(['https://sp.com/acs'])))
            )
            ->addItem((new AttributeStatement())
                ->addAttribute(new Attribute(ClaimTypes::EMAIL_ADDRESS, 'foo@idp.com'))
            )
            ->addItem((new AuthnStatement())
                ->setAuthnInstant(new \DateTime('-1 hour'))
                ->setSessionIndex(Helper::generateID())
                ->setAuthnContext((new AuthnContext())
                    ->setAuthnContextClassRef(SamlConstants::AUTHN_CONTEXT_PASSWORD_PROTECTED_TRANSPORT)
                )
            )
        ;
        $this->sign($assertion);

        $this->sign($response);

        $this->validateProtocol($response);
    }
}
