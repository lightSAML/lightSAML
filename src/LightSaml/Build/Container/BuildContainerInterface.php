<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
