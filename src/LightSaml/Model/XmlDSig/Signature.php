<?php

namespace LightSaml\Model\XmlDSig;

use LightSaml\Model\AbstractSamlModel;

abstract class Signature extends AbstractSamlModel
{
    /**
     * @return string
     */
    protected function getIDName()
    {
        return 'ID';
    }
}
