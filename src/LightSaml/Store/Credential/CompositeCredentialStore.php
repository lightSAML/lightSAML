<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Store\Credential;

use LightSaml\Credential\CredentialInterface;

class CompositeCredentialStore implements CredentialStoreInterface
{
    /** @var CredentialStoreInterface[] */
    protected $stores = [];

    /**
     * @param string $entityId
     *
     * @return CredentialInterface[]
     */
    public function getByEntityId($entityId)
    {
        $result = [];
        foreach ($this->stores as $store) {
            $result = array_merge($result, $store->getByEntityId($entityId));
        }

        return $result;
    }

    /**
     * @return CompositeCredentialStore
     */
    public function add(CredentialStoreInterface $store)
    {
        $this->stores[] = $store;

        return $this;
    }
}
