<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Action\Assertion\Inbound;

use LightSaml\Action\Assertion\AbstractAssertionAction;
use LightSaml\Context\Profile\AssertionContext;
use LightSaml\Error\LightSamlValidationException;
use LightSaml\SamlConstants;
use Psr\Log\LoggerInterface;

class AssertionIssuerFormatValidatorAction extends AbstractAssertionAction
{
    /** @var  string */
    private $expectedIssuerFormat = SamlConstants::NAME_ID_FORMAT_ENTITY;

    /**
     * @param LoggerInterface $logger
     * @param string          $expectedIssuerFormat
     */
    public function __construct(LoggerInterface $logger, $expectedIssuerFormat)
    {
        parent::__construct($logger);

        $this->expectedIssuerFormat = $expectedIssuerFormat;
    }

    /**
     * @param AssertionContext $context
     */
    protected function doExecute(AssertionContext $context)
    {
        if (null == $context->getAssertion()->getIssuer()) {
            throw new LightSamlValidationException('Assertion element must have an issuer element');
        }

        if ($context->getAssertion()->getIssuer()->getFormat() &&
            $context->getAssertion()->getIssuer()->getFormat() != $this->expectedIssuerFormat
        ) {
            throw new LightSamlValidationException(sprintf(
                "Response Issuer Format if set must have value '%s' but it was '%s'",
                $this->expectedIssuerFormat,
                $context->getAssertion()->getIssuer()->getFormat()
            ));
        }
    }
}
