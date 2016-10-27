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

use LightSaml\Provider\Attribute\AttributeValueProviderInterface;
use LightSaml\Provider\NameID\NameIdProviderInterface;
use LightSaml\Provider\Session\SessionInfoProviderInterface;

interface ProviderContainerInterface
{
    /**
     * @return AttributeValueProviderInterface
     */
    public function getAttributeValueProvider();

    /**
     * @return SessionInfoProviderInterface
     */
    public function getSessionInfoProvider();

    /**
     * @return NameIdProviderInterface
     */
    public function getNameIdProvider();
}
