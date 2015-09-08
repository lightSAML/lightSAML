<?php

namespace LightSaml\Builder\Action\Profile\Metadata;

use LightSaml\Action\Profile\Entity\SerializeOwnEntityAction;
use LightSaml\Builder\Action\Profile\AbstractProfileActionBuilder;

class MetadataActionBuilder extends AbstractProfileActionBuilder
{
    /**
     * @return void
     */
    protected function doInitialize()
    {
        $this->add(new SerializeOwnEntityAction($this->buildContainer->getSystemContainer()->getLogger()), 100);
    }
}
