<?php

namespace LightSaml\Tests\Model\Xsd;

use LightSaml\Credential\KeyHelper;
use LightSaml\Credential\X509Certificate;
use LightSaml\Model\Assertion\Assertion;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\Model\Metadata\EntitiesDescriptor;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Model\Protocol\SamlMessage;
use LightSaml\Model\SamlElementInterface;
use LightSaml\Model\XmlDSig\SignatureWriter;

abstract class AbstractXsdValidationTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        libxml_use_internal_errors(true);
    }

    /**
     * @return X509Certificate
     */
    protected function getX509Certificate()
    {
        return X509Certificate::fromFile(__DIR__.'/../../../../../resources/sample/Certificate/saml.crt');
    }

    /**
     * @param SamlMessage|EntityDescriptor|EntitiesDescriptor|Assertion $object
     */
    protected function sign($object)
    {
        $object->setSignature(new SignatureWriter(
            $this->getX509Certificate(),
            KeyHelper::createPrivateKey(__DIR__.'/../../../../../resources/sample/Certificate/saml.pem', '', true)
        ));
    }

    /**
     * @param SamlElementInterface $samlElement
     */
    protected function validateProtocol(SamlElementInterface $samlElement)
    {
        $this->validate($samlElement, 'saml-schema-protocol-2.0.xsd');
    }

    /**
     * @param SamlElementInterface $samlElement
     */
    protected function validateMetadata(SamlElementInterface $samlElement)
    {
        $this->validate($samlElement, 'saml-schema-metadata-2.0.xsd');
    }

    /**
     * @param SamlElementInterface $samlElement
     * @param string               $schema
     */
    private function validate(SamlElementInterface $samlElement, $schema)
    {
        $serializationContext = new SerializationContext();
        $samlElement->serialize($serializationContext->getDocument(), $serializationContext);

        $xml = $serializationContext->getDocument()->saveXML();

        $ok = $serializationContext->getDocument()->schemaValidate(__DIR__.'/../../../../../xsd/'.$schema);

        if ($ok) {
            $this->assertTrue(true);

            return;
        }

        $levels = [
            LIBXML_ERR_WARNING => 'Warning',
            LIBXML_ERR_ERROR => 'Error',
            LIBXML_ERR_FATAL => 'Fatal',
        ];

        $arr = [];
        /** @var \LibXMLError[] $errors */
        $errors = libxml_get_errors();
        foreach ($errors as $error) {
            $level = @$levels[$error->level] ?: 'Unknown';
            $msg = sprintf(
                '%s %s: %s on line %s column %s',
                $level,
                $error->code,
                trim($error->message),
                $error->line,
                $error->column
            );
            $arr[] = $msg;
        }

        $this->fail("\n".implode("\n", $arr)."\n\n$xml\n\n");
    }
}
