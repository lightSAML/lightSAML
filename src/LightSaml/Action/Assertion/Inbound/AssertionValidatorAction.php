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
use LightSaml\Validator\Model\Assertion\AssertionValidatorInterface;
use Psr\Log\LoggerInterface;

class AssertionValidatorAction extends AbstractAssertionAction
{
    /** @var AssertionValidatorInterface */
    protected $assertionValidator;

    public function __construct(LoggerInterface $logger, AssertionValidatorInterface $assertionValidator)
    {
        parent::__construct($logger);

        $this->assertionValidator = $assertionValidator;
    }

    protected function doExecute(AssertionContext $context)
    {
        $this->assertionValidator->validateAssertion($context->getAssertion());
    }
}
