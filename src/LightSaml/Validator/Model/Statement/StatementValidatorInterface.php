<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Validator\Model\Statement;

use LightSaml\Model\Assertion\AbstractStatement;

interface StatementValidatorInterface
{
    /**
     * @param AbstractStatement $statement
     *
     * @return void
     */
    public function validateStatement(AbstractStatement $statement);
}
