<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
