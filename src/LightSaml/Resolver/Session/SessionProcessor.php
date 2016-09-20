<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Resolver\Session;

use LightSaml\Model\Assertion\Assertion;
use LightSaml\Provider\TimeProvider\TimeProviderInterface;
use LightSaml\State\Sso\SsoSessionState;
use LightSaml\State\Sso\SsoState;
use LightSaml\Store\Sso\SsoStateStoreInterface;

class SessionProcessor implements SessionProcessorInterface
{
    /** @var SsoStateStoreInterface */
    protected $ssoStateStore;

    /** @var TimeProviderInterface */
    protected $timeProvider;

    /**
     * @param SsoStateStoreInterface $ssoStateStore
     * @param TimeProviderInterface  $timeProvider
     */
    public function __construct(SsoStateStoreInterface $ssoStateStore, TimeProviderInterface $timeProvider)
    {
        $this->ssoStateStore = $ssoStateStore;
        $this->timeProvider = $timeProvider;
    }

    /**
     * @param Assertion[] $assertions
     * @param string      $ownEntityId
     * @param string      $partyEntityId
     */
    public function processAssertions(array $assertions, $ownEntityId, $partyEntityId)
    {
        $now = $this->timeProvider->getDateTime()->setTimezone(new \DateTimeZone('GMT'));
        $ssoState = $this->ssoStateStore->get();

        foreach ($assertions as $assertion) {
            if ($assertion instanceof Assertion) {
                if ($this->supportsSession($assertion)) {
                    $this->checkSession($ownEntityId, $partyEntityId, $ssoState, $assertion, $now);
                }
            } else {
                throw new \InvalidArgumentException('Expected Assertion');
            }
        }

        $this->ssoStateStore->set($ssoState);
    }

    /**
     * @param Assertion $assertion
     *
     * @return bool
     */
    protected function supportsSession(Assertion $assertion)
    {
        return
            $assertion->hasBearerSubject() &&
            null != $assertion->getSubject() &&
            null != $assertion->getSubject()->getNameID()
            ;
    }

    /**
     * @param string    $ownEntityId
     * @param string    $partyEntityId
     * @param SsoState  $ssoState
     * @param Assertion $assertion
     * @param \DateTime $now
     */
    protected function checkSession($ownEntityId, $partyEntityId, SsoState $ssoState, Assertion $assertion, \DateTime $now)
    {
        $sessions = $this->filterSessions($ssoState, $assertion, $ownEntityId, $partyEntityId);

        if (empty($sessions)) {
            $this->createSession($ssoState, $assertion, $now, $ownEntityId, $partyEntityId);
        } else {
            $this->updateLastAuthn($sessions, $now);
        }
    }

    /**
     * @param SsoState  $ssoState
     * @param Assertion $assertion
     * @param \DateTime $now
     * @param string    $ownEntityId
     * @param string    $partyEntityId
     *
     * @return SsoSessionState
     */
    protected function createSession(SsoState $ssoState, Assertion $assertion, \DateTime $now, $ownEntityId, $partyEntityId)
    {
        $ssoSession = new SsoSessionState();
        $ssoSession->setIdpEntityId($partyEntityId)
            ->setSpEntityId($ownEntityId)
            ->setNameId($assertion->getSubject()->getNameID()->getValue())
            ->setNameIdFormat($assertion->getSubject()->getNameID()->getFormat())
            ->setSessionIndex($assertion->getFirstAuthnStatement()->getSessionIndex())
            ->setSessionInstant($assertion->getFirstAuthnStatement()->getAuthnInstantDateTime())
            ->setFirstAuthOn($now)
            ->setLastAuthOn($now)
        ;
        $ssoState->addSsoSession($ssoSession);

        return $ssoSession;
    }

    /**
     * @param SsoSessionState[] $sessions
     * @param \DateTime         $now
     */
    protected function updateLastAuthn(array $sessions, \DateTime $now)
    {
        foreach ($sessions as $session) {
            $session->setLastAuthOn($now);
        }
    }

    /**
     * @param SsoState  $ssoState
     * @param Assertion $assertion
     * @param string    $ownEntityId
     * @param string    $partyEntityId
     *
     * @return \LightSaml\State\Sso\SsoSessionState[]
     */
    protected function filterSessions(SsoState $ssoState, Assertion $assertion, $ownEntityId, $partyEntityId)
    {
        return $ssoState->filter(
            $partyEntityId,
            $ownEntityId,
            $assertion->getSubject()->getNameID()->getValue(),
            $assertion->getSubject()->getNameID()->getFormat(),
            $assertion->getFirstAuthnStatement()->getSessionIndex()
        );
    }
}
