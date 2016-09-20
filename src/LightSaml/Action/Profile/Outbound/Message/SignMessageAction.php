<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Action\Profile\Outbound\Message;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\Helper\LogHelper;
use LightSaml\Context\Profile\Helper\MessageContextHelper;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Model\Protocol\LogoutRequest;
use LightSaml\Model\Protocol\Response;
use LightSaml\Resolver\Signature\SignatureResolverInterface;
use Psr\Log\LoggerInterface;

/**
 * Signs the outbound message, according to TrustOptions settings.
 */
class SignMessageAction extends AbstractProfileAction
{
    /** @var SignatureResolverInterface */
    protected $signatureResolver;

    /**
     * @param LoggerInterface            $logger
     * @param SignatureResolverInterface $signatureResolver
     */
    public function __construct(LoggerInterface $logger, SignatureResolverInterface $signatureResolver)
    {
        parent::__construct($logger);

        $this->signatureResolver = $signatureResolver;
    }

    protected function doExecute(ProfileContext $context)
    {
        $shouldSign = $this->shouldSignMessage($context);
        if ($shouldSign) {
            $signature = $this->signatureResolver->getSignature($context);
            if ($signature) {
                MessageContextHelper::asSamlMessage($context->getOutboundContext())
                    ->setSignature($signature)
                ;

                $this->logger->debug(
                    sprintf('Message signed with fingerprint "%s"', $signature->getCertificate()->getFingerprint()),
                    LogHelper::getActionContext($context, $this, array(
                        'certificate' => $signature->getCertificate()->getInfo(),
                    ))
                );
            } else {
                $this->logger->critical(
                    'No signature resolved, although signing enabled',
                    LogHelper::getActionErrorContext($context, $this, array())
                );
            }
        } else {
            $this->logger->debug('Signing disabled', LogHelper::getActionContext($context, $this));
        }
    }

    /**
     * @param ProfileContext $context
     *
     * @return bool
     */
    private function shouldSignMessage(ProfileContext $context)
    {
        $message = $context->getOutboundMessage();

        if ($message instanceof LogoutRequest) {
            return true;
        }

        $trustOptions = $context->getTrustOptions();

        if ($message instanceof AuthnRequest) {
            return $trustOptions->getSignAuthnRequest();
        } elseif ($message instanceof Response) {
            return $trustOptions->getSignResponse();
        }

        throw new \LogicException(sprintf('Unexpected message type "%s"', get_class($message)));
    }
}
