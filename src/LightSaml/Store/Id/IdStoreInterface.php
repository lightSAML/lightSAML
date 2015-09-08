<?php

namespace LightSaml\Store\Id;

interface IdStoreInterface
{
    /**
     * @param string    $entityId
     * @param string    $id
     * @param \DateTime $expiryTime
     *
     * @return void
     */
    public function set($entityId, $id, \DateTime $expiryTime);

    /**
     * @param string $entityId
     * @param string $id
     *
     * @return bool
     */
    public function has($entityId, $id);
}
