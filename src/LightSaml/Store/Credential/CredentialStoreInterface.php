<?php

namespace LightSaml\Store\Credential;

use LightSaml\Credential\CredentialInterface;

interface CredentialStoreInterface
{
    /**
     * @param string $entityId
     *
     * @return CredentialInterface[]
     */
    public function getByEntityId($entityId);
}
