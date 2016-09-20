<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Action\Profile\Inbound\Message;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\Helper\LogHelper;
use LightSaml\Context\Profile\Helper\MessageContextHelper;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Error\LightSamlModelException;
use LightSaml\Model\XmlDSig\AbstractSignatureReader;
use LightSaml\Credential\Criteria\MetadataCriteria;
use LightSaml\Validator\Model\Signature\SignatureValidatorInterface;
use Psr\Log\LoggerInterface;

/**
 * Validates the signature, if any, of the inbound message.
 */
class MessageSignatureValidatorAction extends AbstractProfileAction
{
    /** @var SignatureValidatorInterface */
    protected $signatureValidator;

    /**
     * @param LoggerInterface             $logger
     * @param SignatureValidatorInterface $signatureValidator
     */
    public function __construct(LoggerInterface $logger, SignatureValidatorInterface $signatureValidator)
    {
        parent::__construct($logger);

        $this->signatureValidator = $signatureValidator;
    }

    /**
     * @param ProfileContext $context
     *
     * @return void
     */
    protected function doExecute(ProfileContext $context)
    {
        $message = MessageContextHelper::asSamlMessage($context->getInboundContext());

        $signature = $message->getSignature();
        if (null === $signature) {
            $this->logger->debug('Message is not signed', LogHelper::getActionContext($context, $this));

            return;
        }

        if ($signature instanceof AbstractSignatureReader) {
            $metadataType = ProfileContext::ROLE_IDP === $context->getOwnRole() ? MetadataCriteria::TYPE_SP : MetadataCriteria::TYPE_IDP;
            $credential = $this->signatureValidator->validate($signature, $message->getIssuer()->getValue(), $metadataType);
            if ($credential) {
                $keyNames = $credential->getKeyNames();
                $this->logger->debug(
                    sprintf('Message signature validated with key "%s"', implode(', ', $keyNames)),
                    LogHelper::getActionContext($context, $this, array(
                        'credential' => $credential,
                    ))
                );
            } else {
                $this->logger->warning(
                    'Signature verification was not performed',
                    LogHelper::getActionContext($context, $this)
                );
            }
        } else {
            $message = 'Expected AbstractSignatureReader';
            $this->logger->critical($message, LogHelper::getActionErrorContext($context, $this));
            throw new LightSamlModelException($message);
        }
    }
}
