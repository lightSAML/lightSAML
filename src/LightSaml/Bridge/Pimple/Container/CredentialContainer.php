<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Bridge\Pimple\Container;

use LightSaml\Build\Container\CredentialContainerInterface;
use LightSaml\Store\Credential\CredentialStoreInterface;

class CredentialContainer extends AbstractPimpleContainer implements CredentialContainerInterface
{
    const CREDENTIAL_STORE = 'lightsaml.container.credential_store';

    /**
     * @return CredentialStoreInterface
     */
    public function getCredentialStore()
    {
        return $this->pimple[self::CREDENTIAL_STORE];
    }
}
