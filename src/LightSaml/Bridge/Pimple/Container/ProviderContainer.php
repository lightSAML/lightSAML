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

use LightSaml\Build\Container\ProviderContainerInterface;
use LightSaml\Provider\Attribute\AttributeNameProviderInterface;
use LightSaml\Provider\Attribute\AttributeValueProviderInterface;
use LightSaml\Provider\NameID\NameIdProviderInterface;
use LightSaml\Provider\Session\SessionInfoProviderInterface;

class ProviderContainer extends AbstractPimpleContainer implements ProviderContainerInterface
{
    const ATTRIBUTE_VALUE_PROVIDER = 'lightsaml.container.attribute_value_provider';
    const ATTRIBUTE_NAME_PROVIDER = 'lightsaml.container.attribute_name_provider';
    const SESSION_INFO_PROVIDER = 'lightsaml.container.session_info_provider';
    const NAME_ID_PROVIDER = 'lightsaml.container.name_id_provider';

    /**
     * @return AttributeValueProviderInterface
     */
    public function getAttributeValueProvider()
    {
        return $this->pimple[self::ATTRIBUTE_VALUE_PROVIDER];
    }

    /**
     * @return AttributeNameProviderInterface
     */
    public function getAttributeNameProvider()
    {
        return $this->pimple[self::ATTRIBUTE_NAME_PROVIDER];
    }

    /**
     * @return SessionInfoProviderInterface
     */
    public function getSessionInfoProvider()
    {
        return $this->pimple[self::SESSION_INFO_PROVIDER];
    }

    /**
     * @return NameIdProviderInterface
     */
    public function getNameIdProvider()
    {
        return $this->pimple[self::NAME_ID_PROVIDER];
    }
}
