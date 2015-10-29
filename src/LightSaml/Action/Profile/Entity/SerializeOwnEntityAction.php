<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Action\Profile\Entity;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Context\Profile\ProfileContexts;
use LightSaml\Model\Context\SerializationContext;
use Symfony\Component\HttpFoundation\Response;

class SerializeOwnEntityAction extends AbstractProfileAction
{
    /** @var string[] */
    protected $supportedContextTypes = array('application/samlmetadata+xml', 'application/xml', 'text/xml');

    /**
     * @param ProfileContext $context
     */
    protected function doExecute(ProfileContext $context)
    {
        $ownEntityDescriptor = $context->getOwnEntityDescriptor();

        /** @var SerializationContext $serializationContext */
        $serializationContext = $context->getSubContext(ProfileContexts::SERIALIZATION, SerializationContext::class);
        $serializationContext->getDocument()->formatOutput = true;

        $ownEntityDescriptor->serialize($serializationContext->getDocument(), $serializationContext);

        $xml = $serializationContext->getDocument()->saveXML();

        $response = new Response($xml);

        $contentType = 'text/xml';
        $acceptableContentTypes = array_flip($context->getHttpRequest()->getAcceptableContentTypes());
        foreach ($this->supportedContextTypes as $supportedContentType) {
            if (isset($acceptableContentTypes[$supportedContentType])) {
                $contentType = $supportedContentType;
                break;
            }
        }

        $response->headers->replace(array('Content-Type' => $contentType));

        $context->getHttpResponseContext()->setResponse($response);
    }
}
