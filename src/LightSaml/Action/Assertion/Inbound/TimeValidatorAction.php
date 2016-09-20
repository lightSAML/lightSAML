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
use LightSaml\Provider\TimeProvider\TimeProviderInterface;
use LightSaml\Validator\Model\Assertion\AssertionTimeValidatorInterface;
use Psr\Log\LoggerInterface;

class TimeValidatorAction extends AbstractAssertionAction
{
    /** @var AssertionTimeValidatorInterface */
    protected $assertionTimeValidator;

    /** @var TimeProviderInterface */
    protected $timeProvider;

    /** @var int */
    protected $allowedSecondsSkew;

    /**
     * @param LoggerInterface                 $logger
     * @param AssertionTimeValidatorInterface $assertionTimeValidator
     * @param TimeProviderInterface           $timeProvider
     * @param int                             $allowedSecondsSkew
     */
    public function __construct(
        LoggerInterface $logger,
        AssertionTimeValidatorInterface $assertionTimeValidator,
        TimeProviderInterface $timeProvider,
        $allowedSecondsSkew = 120
    ) {
        parent::__construct($logger);

        $this->assertionTimeValidator = $assertionTimeValidator;
        $this->timeProvider = $timeProvider;
        $this->allowedSecondsSkew = $allowedSecondsSkew;
    }

    /**
     * @param AssertionContext $context
     *
     * @return void
     */
    protected function doExecute(AssertionContext $context)
    {
        $this->assertionTimeValidator->validateTimeRestrictions(
            $context->getAssertion(),
            $this->timeProvider->getTimestamp(),
            $this->allowedSecondsSkew
        );
    }
}
