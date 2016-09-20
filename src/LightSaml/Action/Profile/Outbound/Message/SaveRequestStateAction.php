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
use LightSaml\State\Request\RequestState;
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
        $state->setNonce($message);

        $this->requestStore->set($state);
    }
}
