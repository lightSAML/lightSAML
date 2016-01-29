<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Validator\Model\NameId;

use LightSaml\Error\LightSamlValidationException;
use LightSaml\Helper;
use LightSaml\Model\Assertion\AbstractNameID;
use LightSaml\SamlConstants;

class NameIdValidator implements NameIdValidatorInterface
{
    private static $formatValidators = array(
        SamlConstants::NAME_ID_FORMAT_EMAIL => 'validateEmailFormat',
        SamlConstants::NAME_ID_FORMAT_X509_SUBJECT_NAME => 'validateX509SubjectNameFormat',
        SamlConstants::NAME_ID_FORMAT_WINDOWS => 'validateWindowsFormat',
        SamlConstants::NAME_ID_FORMAT_KERBEROS => 'validateKerberosFormat',
        SamlConstants::NAME_ID_FORMAT_ENTITY => 'validateEntityFormat',
        SamlConstants::NAME_ID_FORMAT_PERSISTENT => 'validatePersistentFormat',
        SamlConstants::NAME_ID_FORMAT_TRANSIENT => 'validateTransientFormat',
    );

    /**
     * @param AbstractNameID $nameId
     *
     * @throws \LightSaml\Error\LightSamlValidationException
     *
     * @return void
     */
    public function validateNameId(AbstractNameID $nameId)
    {
        if (false == $nameId->getFormat()) {
            return;
        }

        $this->validateFormat($nameId);

        $validatorMethod = isset(self::$formatValidators[$nameId->getFormat()]) ? self::$formatValidators[$nameId->getFormat()] : null;

        if ($validatorMethod) {
            $this->{$validatorMethod}($nameId);
        }
    }

    /**
     * @param AbstractNameID $nameId
     */
    protected function validateFormat(AbstractNameID $nameId)
    {
        if (false == Helper::validateWellFormedUriString($nameId->getFormat())) {
            throw new LightSamlValidationException(
                sprintf(
                    "NameID element has Format attribute '%s' which is not a wellformed absolute uri",
                    $nameId->getFormat()
                )
            );
        }
    }

    /**
     * @param AbstractNameID $nameId
     */
    protected function validateEmailFormat(AbstractNameID $nameId)
    {
        if (false == Helper::validateRequiredString($nameId->getValue())) {
            throw new LightSamlValidationException('NameID with Email Format attribute MUST contain a Value that contains more than whitespace characters');
        }

        if (false == filter_var($nameId->getValue(), FILTER_VALIDATE_EMAIL)) {
            throw new LightSamlValidationException('Value of NameID is not a valid email address according to the IETF RFC 2822 specification');
        }
    }

    /**
     * @param AbstractNameID $nameId
     */
    protected function validateX509SubjectNameFormat(AbstractNameID $nameId)
    {
        if (false == Helper::validateRequiredString($nameId->getValue())) {
            throw new LightSamlValidationException('NameID with X509SubjectName Format attribute MUST contain a Value that contains more than whitespace characters');
        }

        // TODO: Consider checking for correct encoding of the Value according to the
        // XML Signature Recommendation (http://www.w3.org/TR/xmldsig-core/) section 4.4.4
    }

    /**
     * @param AbstractNameID $nameId
     */
    protected function validateWindowsFormat(AbstractNameID $nameId)
    {
        // Required format is 'DomainName\UserName' but the domain name and the '\' are optional
        if (false == Helper::validateRequiredString($nameId->getValue())) {
            throw new LightSamlValidationException('NameID with Windows Format attribute MUST contain a Value that contains more than whitespace characters');
        }
    }

    /**
     * @param AbstractNameID $nameId
     */
    protected function validateKerberosFormat(AbstractNameID $nameId)
    {
        // Required format is 'name[/instance]@REALM'
        if (false == Helper::validateRequiredString($nameId->getValue())) {
            throw new LightSamlValidationException('NameID with Kerberos Format attribute MUST contain a Value that contains more than whitespace characters');
        }
        if (strlen($nameId->getValue()) < 3) {
            throw new LightSamlValidationException('NameID with Kerberos Format attribute MUST contain a Value with at least 3 characters');
        }
        if (false === strpos($nameId->getValue(), '@')) {
            throw new LightSamlValidationException("NameID with Kerberos Format attribute MUST contain a Value that contains a '@'");
        }
        // TODO: Consider implementing the rules for 'name', 'instance' and 'REALM' found in IETF RFC 1510 (http://www.ietf.org/rfc/rfc1510.txt) here
    }

    /**
     * @param AbstractNameID $nameId
     */
    protected function validateEntityFormat(AbstractNameID $nameId)
    {
        if (false == Helper::validateRequiredString($nameId->getValue())) {
            throw new LightSamlValidationException('NameID with Entity Format attribute MUST contain a Value that contains more than whitespace characters');
        }
        if (strlen($nameId->getValue()) > 1024) {
            throw new LightSamlValidationException('NameID with Entity Format attribute MUST have a Value that contains no more than 1024 characters');
        }
        if (false != $nameId->getNameQualifier()) {
            throw new LightSamlValidationException('NameID with Entity Format attribute MUST NOT set the NameQualifier attribute');
        }
        if (false != $nameId->getSPNameQualifier()) {
            throw new LightSamlValidationException('NameID with Entity Format attribute MUST NOT set the SPNameQualifier attribute');
        }
        if (false != $nameId->getSPProvidedID()) {
            throw new LightSamlValidationException('NameID with Entity Format attribute MUST NOT set the SPProvidedID attribute');
        }
    }

    /**
     * @param AbstractNameID $nameId
     */
    protected function validatePersistentFormat(AbstractNameID $nameId)
    {
        if (false == Helper::validateRequiredString($nameId->getValue())) {
            throw new LightSamlValidationException('NameID with Persistent Format attribute MUST contain a Value that contains more than whitespace characters');
        }
        if (strlen($nameId->getValue()) > 256) {
            throw new LightSamlValidationException('NameID with Persistent Format attribute MUST have a Value that contains no more than 256 characters');
        }
    }

    /**
     * @param AbstractNameID $nameId
     */
    protected function validateTransientFormat(AbstractNameID $nameId)
    {
        if (false == Helper::validateRequiredString($nameId->getValue())) {
            throw new LightSamlValidationException('NameID with Transient Format attribute MUST contain a Value that contains more than whitespace characters');
        }
        if (strlen($nameId->getValue()) > 256) {
            throw new LightSamlValidationException('NameID with Transient Format attribute MUST have a Value that contains no more than 256 characters');
        }
        if (false == Helper::validateIdString($nameId->getValue())) {
            throw new LightSamlValidationException(
                sprintf(
                    "NameID '%s' with Transient Format attribute MUST have a Value with at least 16 characters (the equivalent of 128 bits)",
                    $nameId->getValue()
                )
            );
        }
    }
}
