<?php

namespace LightSaml\Model\Metadata;

use LightSaml\Model\Metadata\Endpoint;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Model\Metadata\RoleDescriptor;

class EndpointReference
{
    /** @var  EntityDescriptor */
    protected $entityDescriptor;

    /** @var RoleDescriptor */
    protected $descriptor;

    /** @var  Endpoint */
    protected $endpoint;

    /**
     * @param EntityDescriptor $entityDescriptor
     * @param RoleDescriptor   $descriptor
     * @param Endpoint         $endpoint
     */
    public function __construct(EntityDescriptor $entityDescriptor, RoleDescriptor $descriptor, Endpoint $endpoint)
    {
        $this->entityDescriptor = $entityDescriptor;
        $this->descriptor = $descriptor;
        $this->endpoint = $endpoint;
    }

    /**
     * @return EntityDescriptor
     */
    public function getEntityDescriptor()
    {
        return $this->entityDescriptor;
    }

    /**
     * @return RoleDescriptor
     */
    public function getDescriptor()
    {
        return $this->descriptor;
    }

    /**
     * @return Endpoint
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }
}
