<?php

namespace LightSaml\Credential\Criteria;

class EntityIdCriteria implements TrustCriteriaInterface
{
    /** @var  string */
    protected $entityId;

    /**
     * @param string $entityId
     */
    public function __construct($entityId)
    {
        $this->entityId = $entityId;
    }

    /**
     * @return string
     */
    public function getEntityId()
    {
        return $this->entityId;
    }
}
