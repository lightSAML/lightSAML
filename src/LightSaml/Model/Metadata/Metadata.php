<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Model\Metadata;

use LightSaml\Error\LightSamlXmlException;
use LightSaml\Model\AbstractSamlModel;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\SamlElementInterface;
use LightSaml\SamlConstants;

abstract class Metadata extends AbstractSamlModel
{
    /**
     * @param string $path
     *
     * @return EntitiesDescriptor|EntityDescriptor
     */
    public static function fromFile($path)
    {
        $deserializatonContext = new DeserializationContext();
        $xml = file_get_contents($path);

        return self::fromXML($xml, $deserializatonContext);
    }

    /**
     * @param string                 $xml
     * @param DeserializationContext $context
     *
     * @return EntityDescriptor|EntitiesDescriptor
     *
     * @throws \Exception
     */
    public static function fromXML($xml, DeserializationContext $context)
    {
        if (false == is_string($xml)) {
            throw new \InvalidArgumentException('Expecting string');
        }

        $context->getDocument()->loadXML($xml);

        $node = $context->getDocument()->firstChild;
        while ($node && $node instanceof \DOMComment) {
            $node = $node->nextSibling;
        }
        if (null === $node) {
            throw new LightSamlXmlException('Empty XML');
        }

        if (SamlConstants::NS_METADATA !== $node->namespaceURI) {
            throw new LightSamlXmlException(sprintf(
                "Invalid namespace '%s' of the root XML element, expected '%s'",
                $node->namespaceURI,
                SamlConstants::NS_METADATA
            ));
        }

        $map = array(
            'EntityDescriptor' => '\LightSaml\Model\Metadata\EntityDescriptor',
            'EntitiesDescriptor' => '\LightSaml\Model\Metadata\EntitiesDescriptor',
        );

        $rootElementName = $node->localName;

        if (array_key_exists($rootElementName, $map)) {
            if ($class = $map[$rootElementName]) {
                /** @var SamlElementInterface $result */
                $result = new $class();
            } else {
                throw new \LogicException('Deserialization of %s root element is not implemented');
            }
        } else {
            throw new LightSamlXmlException(sprintf("Unknown SAML metadata '%s'", $rootElementName));
        }

        $result->deserialize($node, $context);

        return $result;
    }
}
