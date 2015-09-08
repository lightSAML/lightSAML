<?php

namespace LightSaml\Validator\Model\NameId;

use LightSaml\Model\Assertion\AbstractNameID;

interface NameIdValidatorInterface
{
    /**
     * @param AbstractNameID $nameId
     *
     * @return void
     */
    public function validateNameId(AbstractNameID $nameId);
}
