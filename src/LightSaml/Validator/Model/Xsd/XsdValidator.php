<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Validator\Model\Xsd;

use LightSaml\Error\LightSamlXmlException;

class XsdValidator
{
    /**
     * @param string $xml
     *
     * @return XsdError[]
     */
    public function validateProtocol($xml)
    {
        return $this->validate($xml, 'saml-schema-protocol-2.0.xsd');
    }

    /**
     * @param string $xml
     *
     * @return XsdError[]
     */
    public function validateMetadata($xml)
    {
        return $this->validate($xml, 'saml-schema-metadata-2.0.xsd');
    }

    /**
     * @param string $xml
     * @param string $schema
     *
     * @return XsdError[]
     */
    private function validate($xml, $schema)
    {
        $result = [];
        libxml_clear_errors();
        $doc = new \DOMDocument();

        set_error_handler(function ($errno, $errstr, $errfile, $errline, array $errcontext) use (&$result) {
            $error = new XsdError(XsdError::FATAL, $errno, $errstr, 0, 0);
            $result[] = $error;
        });

        $schemaFile = __DIR__.'/../../../../../xsd/'.$schema;
        if (!is_file($schemaFile)) {
            throw new LightSamlXmlException('Invalid schema specified');
        }

        $ok = @$doc->loadXML($xml);
        if (!$ok) {
            restore_error_handler();

            return [
                new XsdError(XsdError::FATAL, 0, 'Invalid XML', 0, 0),
            ];
        }

        @$doc->schemaValidate($schemaFile);

        /** @var \LibXMLError[] $errors */
        $errors = libxml_get_errors();
        foreach ($errors as $error) {
            $err = XsdError::fromLibXMLError($error);
            $result[] = $err;
        }

        restore_error_handler();

        return $result;
    }
}
