<?php

namespace LightSaml\Tests\Action\Profile\Outbound\Message;

use LightSaml\Action\Profile\Outbound\Message\SignMessageAction;
use LightSaml\Meta\TrustOptions\TrustOptions;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Model\Protocol\Response;
use LightSaml\Model\Protocol\SamlMessage;
use LightSaml\Model\XmlDSig\SignatureWriter;
use LightSaml\Tests\BaseTestCase;

class SignMessageActionTest extends BaseTestCase
{
    public function test_constructs_with_logger_and_signature_resolver()
    {
        new SignMessageAction($this->getLoggerMock(), $this->getSignatureResolverMock());
        $this->assertTrue(true);
    }

    public function supports_message_provider()
    {
        return [
            ['setSignAuthnRequest', new AuthnRequest()],
            ['setSignResponse', new Response()],
        ];
    }

    /**
     * @dataProvider supports_message_provider
     */
    public function test_supports_message($trustOptionsMethod, SamlMessage $message)
    {
        $action = new SignMessageAction($this->getLoggerMock(), $this->getSignatureResolverMock());

        $context = $this->getProfileContext();
        $context->getPartyEntityContext()->setTrustOptions(new TrustOptions());
        $context->getTrustOptions()->{$trustOptionsMethod}(false);
        $context->getOutboundContext()->setMessage($message);

        $action->execute($context);

        $this->assertTrue(true);
    }

    public function does_not_support_message_provider()
    {
        return [
            [$this->getMockForAbstractClass(SamlMessage::class)],
        ];
    }

    /**
     * @dataProvider does_not_support_message_provider
     *
     *
     */
    public function test_does_not_support_message(SamlMessage $message)
    {
        $this->expectExceptionMessage("Unexpected message type");
        $this->expectException(\LogicException::class);
        $action = new SignMessageAction($this->getLoggerMock(), $this->getSignatureResolverMock());

        $context = $this->getProfileContext();
        $context->getPartyEntityContext()->setTrustOptions(new TrustOptions());
        $context->getOutboundContext()->setMessage($message);

        $action->execute($context);
    }

    public function test_logs_disabled_signing()
    {
        $action = new SignMessageAction(
            $loggerMock = $this->getLoggerMock(),
            $signatureResolverMock = $this->getSignatureResolverMock()
        );

        $context = $this->getProfileContext();
        $context->getPartyEntityContext()->setTrustOptions(new TrustOptions());
        $context->getTrustOptions()->setSignAuthnRequest(false);
        $context->getOutboundContext()->setMessage($message = new AuthnRequest());

        $loggerMock->expects($this->once())
            ->method('debug')
            ->with('Signing disabled', $this->isType('array'));

        $signatureResolverMock->expects($this->never())
            ->method('getSignature');

        $action->execute($context);
    }

    public function test_logs_no_signature_resolved()
    {
        $action = new SignMessageAction(
            $loggerMock = $this->getLoggerMock(),
            $signatureResolverMock = $this->getSignatureResolverMock()
        );

        $context = $this->getProfileContext();
        $context->getPartyEntityContext()->setTrustOptions(new TrustOptions());
        $context->getTrustOptions()->setSignAuthnRequest(true);
        $context->getOutboundContext()->setMessage($message = new AuthnRequest());

        $loggerMock->expects($this->once())
            ->method('critical')
            ->with('No signature resolved, although signing enabled', $this->isType('array'));

        $action->execute($context);
    }

    public function test_signs_message_when_signing_enabled()
    {
        $action = new SignMessageAction(
            $loggerMock = $this->getLoggerMock(),
            $signatureResolverMock = $this->getSignatureResolverMock()
        );

        $context = $this->getProfileContext();
        $context->getPartyEntityContext()->setTrustOptions(new TrustOptions());
        $context->getTrustOptions()->setSignAuthnRequest(true);
        $context->getOutboundContext()->setMessage($message = new AuthnRequest());

        $signature = new SignatureWriter($certificateMock = $this->getX509CertificateMock());
        $certificateMock->expects($this->any())
            ->method('getInfo')
            ->willReturn($expectedInfo = ['a'=>1]);
        $certificateMock->expects($this->any())
            ->method('getFingerprint')
            ->willReturn($expectedFingerprint = '123123123');

        $signatureResolverMock->expects($this->once())
            ->method('getSignature')
            ->with($context)
            ->willReturn($signature);

        $loggerMock->expects($this->once())
            ->method('debug')
            ->with('Message signed with fingerprint "123123123"', $this->isType('array'));

        $action->execute($context);

        $this->assertSame($signature, $message->getSignature());
    }
}
