<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
