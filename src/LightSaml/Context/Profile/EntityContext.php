<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Context\Profile;

use LightSaml\Meta\TrustOptions\TrustOptions;
use LightSaml\Model\Metadata\EntityDescriptor;

class EntityContext extends AbstractProfileContext
{
    /** @var string */
    private $entityId;

    /** @var EntityDescriptor */
    private $entityDescriptor;

    /** @var TrustOptions */
    private $trustOptions;

    /**
     * @return string
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * @param string $entityId
     *
     * @return EntityContext
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;

        return $this;
    }

    /**
     * @return EntityDescriptor
     */
    public function getEntityDescriptor()
    {
        return $this->entityDescriptor;
    }

    /**
     * @param EntityDescriptor $entityDescriptor
     *
     * @return EntityContext
     */
    public function setEntityDescriptor(EntityDescriptor $entityDescriptor)
    {
        $this->entityDescriptor = $entityDescriptor;

        return $this;
    }

    /**
     * @return TrustOptions
     */
    public function getTrustOptions()
    {
        return $this->trustOptions;
    }

    /**
     * @param TrustOptions $trustOptions
     *
     * @return EntityContext
     */
    public function setTrustOptions(TrustOptions $trustOptions)
    {
        $this->trustOptions = $trustOptions;

        return $this;
    }
}
