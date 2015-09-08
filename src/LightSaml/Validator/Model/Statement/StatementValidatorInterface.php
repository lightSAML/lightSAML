<?php

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
