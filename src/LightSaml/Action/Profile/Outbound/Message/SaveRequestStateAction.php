<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Action\Profile\Outbound\Message;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\Helper\MessageContextHelper;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Protocol\LogoutRequest;
use LightSaml\State\Request\RequestState;
use LightSaml\State\Request\RequestStateParameters;
use LightSaml\Store\Request\RequestStateStoreInterface;
use Psr\Log\LoggerInterface;

class SaveRequestStateAction extends AbstractProfileAction
{
    /** @var RequestStateStoreInterface */
    protected $requestStore;

    /**
     * @param LoggerInterface            $logger
     * @param RequestStateStoreInterface $requestStore
     */
    public function __construct(LoggerInterface $logger, RequestStateStoreInterface $requestStore)
    {
        parent::__construct($logger);

        $this->requestStore = $requestStore;
    }

    /**
     * @param ProfileContext $context
     */
    protected function doExecute(ProfileContext $context)
    {
        $message = MessageContextHelper::asSamlMessage($context->getOutboundContext());

        $state = new RequestState();
        $state->setId($message->getID());

        $partyEntityId = $context->getPartyEntityContext() ? $context->getPartyEntityContext()->getEntityId() : '';
        if ($context->getPartyEntityContext() && $context->getPartyEntityContext()->getEntityDescriptor()) {
            $partyEntityId = $context->getPartyEntityContext()->getEntityDescriptor()->getEntityID();
        }

        $state->getParameters()->add([
            RequestStateParameters::ID => $message->getID(),
            RequestStateParameters::TYPE => get_class($message),
            RequestStateParameters::TIMESTAMP => $message->getIssueInstantTimestamp(),
            RequestStateParameters::PARTY => $partyEntityId,
            RequestStateParameters::RELAY_STATE => $message->getRelayState(),
        ]);

        if ($message instanceof LogoutRequest) {
            $state->getParameters()->add([
                RequestStateParameters::NAME_ID => $message->getNameID()->getValue(),
                RequestStateParameters::NAME_ID_FORMAT => $message->getNameID()->getFormat(),
                RequestStateParameters::SESSION_INDEX => $message->getSessionIndex(),
            ]);
        }

        $this->requestStore->set($state);
    }
}
