<?php

namespace LightSaml\Tests\Action\Assertion\Inbound;

use LightSaml\Action\Assertion\Inbound\AssertionIssuerFormatValidatorAction;
use LightSaml\Model\Assertion\Assertion;
use LightSaml\Model\Assertion\Issuer;
use LightSaml\SamlConstants;
use LightSaml\Tests\TestHelper;

class AssertionIssuerFormatValidatorActionTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_with_logger_and_name_id_format()
    {
        new AssertionIssuerFormatValidatorAction(TestHelper::getLoggerMock($this), $expectedIssuerFormat = SamlConstants::NAME_ID_FORMAT_EMAIL);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlContextException
     * @expectedExceptionMessage Assertion element must have an issuer element
     */
    public function test_throws_context_exception_when_assertion_has_no_issuer()
    {
        $action = new AssertionIssuerFormatValidatorAction(
            $loggerMock = TestHelper::getLoggerMock($this),
            $expectedIssuerFormat = SamlConstants::NAME_ID_FORMAT_EMAIL
        );

        $context = TestHelper::getAssertionContext($assertion = new Assertion());

        $loggerMock->expects($this->once())
            ->method('error')
            ->with('Assertion element must have an issuer element', $this->isType('array'));

        $action->execute($context);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlContextException
     * @expectedExceptionMessage Response Issuer Format if set must have value 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress' but it was 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent'
     */
    public function test_throws_context_exception_when_assertion_issuer_format_does_not_matches_expected_format()
    {
        $action = new AssertionIssuerFormatValidatorAction(
            $loggerMock = TestHelper::getLoggerMock($this),
            $expectedIssuerFormat = SamlConstants::NAME_ID_FORMAT_EMAIL
        );

        $context = TestHelper::getAssertionContext($assertion = new Assertion());
        $assertion->setIssuer(new Issuer('http://issuer.com', $issuerFormat = SamlConstants::NAME_ID_FORMAT_PERSISTENT));

        $loggerMock->expects($this->once())
            ->method('error')
            ->with(
                "Response Issuer Format if set must have value 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress' but it was 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent'",
                $this->isType('array')
            );

        $action->execute($context);
    }

    public function test_does_nothing_if_issuer_has_no_format()
    {
        $action = new AssertionIssuerFormatValidatorAction(
            $loggerMock = TestHelper::getLoggerMock($this),
            $expectedIssuerFormat = SamlConstants::NAME_ID_FORMAT_EMAIL
        );

        $context = TestHelper::getAssertionContext($assertion = new Assertion());
        $assertion->setIssuer(new Issuer('http://issuer.com'));

        $action->execute($context);
    }
}
