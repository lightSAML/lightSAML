<?php

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
 * Validates the signature, if any, of the inbound message
 */
class MessageSignatureValidatorAction extends AbstractProfileAction
{
    /** @var  SignatureValidatorInterface */
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
            $key = $this->signatureValidator->validate($signature, $message->getIssuer()->getValue(), $metadataType);
            if ($key) {
                $certificateInfo = openssl_x509_parse($key->getX509Certificate());
                $this->logger->debug(
                    sprintf('Message signature validated with key name "%s", fingerprint "%s"', $certificateInfo['name'], $key->getX509Thumbprint()),
                    LogHelper::getActionContext($context, $this, array(
                      'key' => $key->key,
                      'certificate' => $certificateInfo,
                    ))
                );
            } else {
                $this->logger->warning(
                    'Signature verification was not performed',
                    LogHelper::getActionContext($context, $this)
                );
            }
        } else {
            throw new LightSamlModelException('Expected AbstractSignatureReader');
        }
    }
}
