<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Binding;

use LightSaml\Error\LightSamlBindingException;
use LightSaml\SamlConstants;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class BindingFactory implements BindingFactoryInterface
{
    /** @var EventDispatcherInterface|null */
    protected $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher = null)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param null|EventDispatcherInterface $eventDispatcher
     *
     * @return BindingFactoryInterface
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher = null)
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    /**
     * @param Request $request
     *
     * @return AbstractBinding
     */
    public function getBindingByRequest(Request $request)
    {
        $bindingType = $this->detectBindingType($request);

        return $this->create($bindingType);
    }

    /**
     * @param string $bindingType
     *
     * @throws \LogicException
     * @throws \LightSaml\Error\LightSamlBindingException
     *
     * @return AbstractBinding
     */
    public function create($bindingType)
    {
        $result = null;
        switch ($bindingType) {
            case SamlConstants::BINDING_SAML2_HTTP_REDIRECT:
                $result = new HttpRedirectBinding();
                break;

            case SamlConstants::BINDING_SAML2_HTTP_POST:
                $result = new HttpPostBinding();
                break;

            case SamlConstants::BINDING_SAML2_HTTP_ARTIFACT:
                throw new \LogicException('Artifact binding not implemented');
            case SamlConstants::BINDING_SAML2_SOAP:
                throw new \LogicException('SOAP binding not implemented');
        }

        if ($result) {
            $result->setEventDispatcher($this->eventDispatcher);

            return $result;
        }

        throw new LightSamlBindingException(sprintf("Unknown binding type '%s'", $bindingType));
    }

    /**
     * @param Request $request
     *
     * @return string|null
     */
    public function detectBindingType(Request $request)
    {
        $requestMethod = trim(strtoupper($request->getMethod()));
        if ($requestMethod == 'GET') {
            return $this->processGET($request);
        } elseif ($requestMethod == 'POST') {
            return $this->processPOST($request);
        }

        return null;
    }

    /**
     * @param Request $request
     *
     * @return null|string
     */
    protected function processGET(Request $request)
    {
        $get = $request->query->all();
        if (array_key_exists('SAMLRequest', $get) || array_key_exists('SAMLResponse', $get)) {
            return SamlConstants::BINDING_SAML2_HTTP_REDIRECT;
        } elseif (array_key_exists('SAMLart', $get)) {
            return SamlConstants::BINDING_SAML2_HTTP_ARTIFACT;
        }

        return null;
    }

    /**
     * @param Request $request
     *
     * @return null|string
     */
    protected function processPOST(Request $request)
    {
        $post = $request->request->all();
        if (array_key_exists('SAMLRequest', $post) || array_key_exists('SAMLResponse', $post)) {
            return SamlConstants::BINDING_SAML2_HTTP_POST;
        } elseif (array_key_exists('SAMLart', $post)) {
            return SamlConstants::BINDING_SAML2_HTTP_ARTIFACT;
        } else {
            if ($contentType = $request->headers->get('CONTENT_TYPE')) {
                // Remove charset
                if (false !== $pos = strpos($contentType, ';')) {
                    $contentType = substr($contentType, 0, $pos);
                }

                if ($contentType === 'text/xml') {
                    return SamlConstants::BINDING_SAML2_SOAP;
                }
            }
        }

        return null;
    }
}
