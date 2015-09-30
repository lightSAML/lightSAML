<?php

namespace LightSaml\Store\EntityDescriptor;

use LightSaml\Model\Metadata\EntityDescriptor;

class CompositeEntityDescriptorStore implements EntityDescriptorStoreInterface
{
    /** @var EntityDescriptorStoreInterface[] */
    private $children = [];

    /**
     * @param EntityDescriptorStoreInterface[] $stores
     */
    public function __construct(array $stores = array())
    {
        foreach ($stores as $store) {
            $this->add($store);
        }
    }

    /**
     * @param EntityDescriptorStoreInterface $store
     *
     * @return CompositeEntityDescriptorStore This instance
     */
    public function add(EntityDescriptorStoreInterface $store)
    {
        $this->children[] = $store;

        return $this;
    }

    /**
     * @param string $entityId
     *
     * @return EntityDescriptor|null
     */
    public function get($entityId)
    {
        foreach ($this->children as $store) {
            $result = $store->get($entityId);
            if ($result) {
                return $result;
            }
        }

        return null;
    }

    /**
     * @param string $entityId
     *
     * @return bool
     */
    public function has($entityId)
    {
        foreach ($this->children as $store) {
            if ($store->has($entityId)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array|EntityDescriptor[]
     */
    public function all()
    {
        $result = array();
        foreach ($this->children as $store) {
            $result = array_merge($result, $store->all());
        }

        return $result;
    }
}
