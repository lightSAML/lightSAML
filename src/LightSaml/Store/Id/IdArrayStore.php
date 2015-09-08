<?php

namespace LightSaml\Store\Id;

class IdArrayStore implements IdStoreInterface
{
    /** @var array  */
    protected $store = array();

    /**
     * @param string    $entityId
     * @param string    $id
     * @param \DateTime $expiryTime
     *
     * @return void
     */
    public function set($entityId, $id, \DateTime $expiryTime)
    {
        if (false == isset($this->store[$entityId])) {
            $this->store[$entityId] = array();
        }
        $this->store[$entityId][$id] = $expiryTime;
    }

    /**
     * @param string $entityId
     * @param string $id
     *
     * @return bool
     */
    public function has($entityId, $id)
    {
        return isset($this->store[$entityId][$id]);
    }
}
