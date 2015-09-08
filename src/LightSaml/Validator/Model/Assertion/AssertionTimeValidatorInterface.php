<?php

namespace LightSaml\Validator\Model\Assertion;

use LightSaml\Model\Assertion\Assertion;

interface AssertionTimeValidatorInterface
{
    /**
     * @param Assertion $assertion
     * @param int       $now
     * @param int       $allowedSecondsSkew
     *
     * @throws \LightSaml\Error\LightSamlValidationException
     *
     * @return void
     */
    public function validateTimeRestrictions(Assertion $assertion, $now, $allowedSecondsSkew);
}
