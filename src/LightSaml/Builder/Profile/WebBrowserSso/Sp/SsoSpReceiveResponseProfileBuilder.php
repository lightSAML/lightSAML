<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Builder\Profile\WebBrowserSso\Sp;

use LightSaml\Builder\Action\Profile\SingleSignOn\Sp\SsoSpReceiveResponseActionBuilder;
use LightSaml\Builder\Action\Profile\SingleSignOn\Sp\SsoSpValidateAssertionActionBuilder;
use LightSaml\Builder\Profile\AbstractProfileBuilder;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Profile\Profiles;

class SsoSpReceiveResponseProfileBuilder extends AbstractProfileBuilder
{
    /**
     * @return string
     */
    protected function getProfileId()
    {
        return Profiles::SSO_SP_RECEIVE_RESPONSE;
    }

    /**
     * @return string
     */
    protected function getProfileRole()
    {
        return ProfileContext::ROLE_SP;
    }

    /**
     * @return \LightSaml\Builder\Action\ActionBuilderInterface
     */
    protected function getActionBuilder()
    {
        $result = new SsoSpReceiveResponseActionBuilder(
            $this->container,
            new SsoSpValidateAssertionActionBuilder($this->container)
        );

        return $result;
    }
}
