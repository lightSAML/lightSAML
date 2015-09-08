<?php

namespace LightSaml\Build\Container;

interface BuildContainerInterface
{
    /**
     * @return SystemContainerInterface
     */
    public function getSystemContainer();

    /**
     * @return PartyContainerInterface
     */
    public function getPartyContainer();

    /**
     * @return StoreContainerInterface
     */
    public function getStoreContainer();

    /**
     * @return ProviderContainerInterface
     */
    public function getProviderContainer();

    /**
     * @return CredentialContainerInterface
     */
    public function getCredentialContainer();

    /**
     * @return ServiceContainerInterface
     */
    public function getServiceContainer();

    /**
     * @return OwnContainerInterface
     */
    public function getOwnContainer();
}
