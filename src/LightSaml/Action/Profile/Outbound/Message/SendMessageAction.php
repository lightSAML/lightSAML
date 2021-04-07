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
use LightSaml\Binding\BindingFactoryInterface;
use LightSaml\Context\Profile\Helper\LogHelper;
use LightSaml\Context\Profile\ProfileContext;
use Psr\Log\LoggerInterface;

class SendMessageAction extends AbstractProfileAction
{
    /** @var BindingFactoryInterface */
    protected $bindingFactory;

    public function __construct(LoggerInterface $logger, BindingFactoryInterface $bindingFactory)
    {
        parent::__construct($logger);

        $this->bindingFactory = $bindingFactory;
    }

    /**
     * @return void
     */
    public function doExecute(ProfileContext $context)
    {
        $binding = $this->bindingFactory->create($context->getEndpoint()->getBinding());

        $outboundContext = $context->getOutboundContext();

        $context->getHttpResponseContext()->setResponse(
            $binding->send($outboundContext)
        );

        $this->logger->info(
            'Sending message',
            LogHelper::getActionContext($context, $this, [
                'message' => $outboundContext->getSerializationContext()->getDocument()->saveXML(),
            ])
        );
    }
}
