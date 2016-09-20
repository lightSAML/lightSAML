<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Context\Profile;

use LightSaml\State\Sso\SsoSessionState;

class LogoutContext extends AbstractProfileContext
{
    /** @var SsoSessionState|null */
    protected $ssoSessionState;

    /** @var bool */
    protected $allSsoSessionsTerminated = false;

    /**
     * @return SsoSessionState|null
     */
    public function getSsoSessionState()
    {
        return $this->ssoSessionState;
    }

    /**
     * @param SsoSessionState $ssoSessionState
     *
     * @return LogoutContext
     */
    public function setSsoSessionState(SsoSessionState $ssoSessionState)
    {
        $this->ssoSessionState = $ssoSessionState;
        $this->allSsoSessionsTerminated = false;

        return $this;
    }

    /**
     * @return bool
     */
    public function areAllSsoSessionsTerminated()
    {
        return $this->allSsoSessionsTerminated;
    }

    /**
     * @param bool $allSsoSessionsTerminated
     *
     * @return LogoutContext
     */
    public function setAllSsoSessionsTerminated($allSsoSessionsTerminated)
    {
        $this->allSsoSessionsTerminated = (bool) $allSsoSessionsTerminated;

        return $this;
    }
}
