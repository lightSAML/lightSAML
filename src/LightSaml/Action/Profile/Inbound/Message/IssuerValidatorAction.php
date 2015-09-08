<?php

namespace LightSaml\Action\Profile\Inbound\Message;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\Helper\LogHelper;
use LightSaml\Context\Profile\Helper\MessageContextHelper;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Error\LightSamlValidationException;
use LightSaml\Validator\Model\NameId\NameIdValidatorInterface;
use Psr\Log\LoggerInterface;

class IssuerValidatorAction extends AbstractProfileAction
{
    /** @var  NameIdValidatorInterface */
    protected $nameIdValidator;

    /** @var  string */
    protected $allowedFormat;

    /**
     * @param LoggerInterface          $logger
     * @param NameIdValidatorInterface $nameIdValidator
     * @param string                   $allowedFormat
     */
    public function __construct(LoggerInterface $logger, NameIdValidatorInterface $nameIdValidator, $allowedFormat)
    {
        parent::__construct($logger);

        $this->nameIdValidator = $nameIdValidator;
        $this->allowedFormat = $allowedFormat;
    }

    /**
     * @param ProfileContext $context
     *
     * @return void
     */
    protected function doExecute(ProfileContext $context)
    {
        $message = MessageContextHelper::asSamlMessage($context->getInboundContext());

        if (false == $message->getIssuer()) {
            $message = sprintf('Inbound message "%s" must have Issuer element', get_class($message));
            $this->logger->emergency($message, LogHelper::getActionErrorContext($context, $this));
            throw new LightSamlValidationException($message);
        }

        if ($this->allowedFormat &&
            $message->getIssuer()->getValue() &&
            $message->getIssuer()->getFormat() &&
            $message->getIssuer()->getFormat() != $this->allowedFormat
        ) {
            $message = sprintf(
                "Response Issuer Format if set must have value '%s' but it was '%s'",
                $this->allowedFormat,
                $message->getIssuer()->getFormat()
            );
            $this->logger->emergency($message, LogHelper::getActionErrorContext($context, $this));
            throw new LightSamlValidationException($message);
        }

        $this->nameIdValidator->validateNameId($message->getIssuer());
    }
}
