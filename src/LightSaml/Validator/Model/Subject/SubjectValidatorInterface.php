<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Validator\Model\Subject;

use LightSaml\Model\Assertion\Subject;

interface SubjectValidatorInterface
{
    /**
     * @param Subject $subject
     *
     * @return void
     */
    public function validateSubject(Subject $subject);
}
