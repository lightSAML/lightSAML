<?php

namespace LightSaml\Tests\Validator\Model\NameId;

use LightSaml\Model\Assertion\NameID;
use LightSaml\SamlConstants;
use LightSaml\Validator\Model\NameId\NameIdValidator;

class NameIdValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function test_ok_if_no_format()
    {
        $nameId = new NameID();

        $validator = new NameIdValidator();

        $validator->validateNameId($nameId);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage NameID element has Format attribute 'invalid format' which is not a wellformed absolute uri
     */
    public function test_invalid_format()
    {
        $nameId = new NameID();
        $nameId->setFormat('invalid format');

        $validator = new NameIdValidator();

        $validator->validateNameId($nameId);
    }

    public function test_valid_email_format()
    {
        $nameId = new NameID();
        $nameId->setFormat(SamlConstants::NAME_ID_FORMAT_EMAIL)
            ->setValue('email@domain.com');

        $validator = new NameIdValidator();

        $validator->validateNameId($nameId);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage Value of NameID is not a valid email address according to the IETF RFC 2822 specification
     */
    public function test_invalid_email_format()
    {
        $nameId = new NameID();
        $nameId->setFormat(SamlConstants::NAME_ID_FORMAT_EMAIL)
            ->setValue('not_an_email');

        $validator = new NameIdValidator();

        $validator->validateNameId($nameId);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage NameID with Email Format attribute MUST contain a Value that contains more than whitespace characters
     */
    public function test_empty_email_format()
    {
        $nameId = new NameID();
        $nameId->setFormat(SamlConstants::NAME_ID_FORMAT_EMAIL);

        $validator = new NameIdValidator();

        $validator->validateNameId($nameId);
    }

    public function test_valid_x509_subject_format()
    {
        $nameId = new NameID();
        $nameId->setFormat(SamlConstants::NAME_ID_FORMAT_X509_SUBJECT_NAME)
            ->setValue('CN=mt.evo.team,O=BOS,C=RS');

        $validator = new NameIdValidator();

        $validator->validateNameId($nameId);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage NameID with X509SubjectName Format attribute MUST contain a Value that contains more than whitespace characters
     */
    public function test_empty_x509_subject_format()
    {
        $nameId = new NameID();
        $nameId->setFormat(SamlConstants::NAME_ID_FORMAT_X509_SUBJECT_NAME);

        $validator = new NameIdValidator();

        $validator->validateNameId($nameId);
    }

    public function test_valid_windows_format_with_domain()
    {
        $nameId = new NameID();
        $nameId->setFormat(SamlConstants::NAME_ID_FORMAT_WINDOWS)
            ->setValue('DomainName\UserName');

        $validator = new NameIdValidator();

        $validator->validateNameId($nameId);
    }

    public function test_valid_windows_format_with_out_domain()
    {
        $nameId = new NameID();
        $nameId->setFormat(SamlConstants::NAME_ID_FORMAT_WINDOWS)
            ->setValue('UserName');

        $validator = new NameIdValidator();

        $validator->validateNameId($nameId);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage NameID with Windows Format attribute MUST contain a Value that contains more than whitespace characters
     */
    public function test_empty_windows_format()
    {
        $nameId = new NameID();
        $nameId->setFormat(SamlConstants::NAME_ID_FORMAT_WINDOWS);

        $validator = new NameIdValidator();

        $validator->validateNameId($nameId);
    }

    public function test_valid_kerberos_format_full()
    {
        $nameId = new NameID();
        $nameId->setFormat(SamlConstants::NAME_ID_FORMAT_KERBEROS)
            ->setValue('name/instance@REALM');

        $validator = new NameIdValidator();

        $validator->validateNameId($nameId);
    }

    public function test_valid_kerberos_format_short()
    {
        $nameId = new NameID();
        $nameId->setFormat(SamlConstants::NAME_ID_FORMAT_KERBEROS)
            ->setValue('name@REALM');

        $validator = new NameIdValidator();

        $validator->validateNameId($nameId);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage NameID with Kerberos Format attribute MUST contain a Value that contains a '@'
     */
    public function test_invalid_kerberos_format()
    {
        $nameId = new NameID();
        $nameId->setFormat(SamlConstants::NAME_ID_FORMAT_KERBEROS)
            ->setValue('name');

        $validator = new NameIdValidator();

        $validator->validateNameId($nameId);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage NameID with Kerberos Format attribute MUST contain a Value with at least 3 characters
     */
    public function test_invalid_kerberos_format_short()
    {
        $nameId = new NameID();
        $nameId->setFormat(SamlConstants::NAME_ID_FORMAT_KERBEROS)
            ->setValue('a@');

        $validator = new NameIdValidator();

        $validator->validateNameId($nameId);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage NameID with Kerberos Format attribute MUST contain a Value that contains more than whitespace characters
     */
    public function test_invalid_kerberos_format_empty()
    {
        $nameId = new NameID();
        $nameId->setFormat(SamlConstants::NAME_ID_FORMAT_KERBEROS);

        $validator = new NameIdValidator();

        $validator->validateNameId($nameId);
    }

    public function test_valid_entity_format()
    {
        $nameId = new NameID();
        $nameId->setFormat(SamlConstants::NAME_ID_FORMAT_ENTITY)
            ->setValue('some:entity');

        $validator = new NameIdValidator();

        $validator->validateNameId($nameId);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage NameID with Entity Format attribute MUST contain a Value that contains more than whitespace characters
     */
    public function test_invalid_entity_format_empty()
    {
        $nameId = new NameID();
        $nameId->setFormat(SamlConstants::NAME_ID_FORMAT_ENTITY);

        $validator = new NameIdValidator();

        $validator->validateNameId($nameId);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage NameID with Entity Format attribute MUST have a Value that contains no more than 1024 characters
     */
    public function test_invalid_entity_format_long()
    {
        $nameId = new NameID();
        $nameId->setFormat(SamlConstants::NAME_ID_FORMAT_ENTITY)
            ->setValue(str_pad('long_string', 1030, 'x'));

        $validator = new NameIdValidator();

        $validator->validateNameId($nameId);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage NameID with Entity Format attribute MUST NOT set the NameQualifier attribute
     */
    public function test_invalid_entity_format_with_name_qualifier()
    {
        $nameId = new NameID();
        $nameId->setFormat(SamlConstants::NAME_ID_FORMAT_ENTITY)
            ->setValue('some:entity')
            ->setNameQualifier('name:qualifier');

        $validator = new NameIdValidator();

        $validator->validateNameId($nameId);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage NameID with Entity Format attribute MUST NOT set the SPNameQualifier attribute
     */
    public function test_invalid_entity_format_with_sp_name_qualifier()
    {
        $nameId = new NameID();
        $nameId->setFormat(SamlConstants::NAME_ID_FORMAT_ENTITY)
            ->setValue('some:entity')
            ->setSPNameQualifier('sp:name:qualifier');

        $validator = new NameIdValidator();

        $validator->validateNameId($nameId);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage NameID with Entity Format attribute MUST NOT set the SPProvidedID attribute
     */
    public function test_invalid_entity_format_with_sp_provided_id()
    {
        $nameId = new NameID();
        $nameId->setFormat(SamlConstants::NAME_ID_FORMAT_ENTITY)
            ->setValue('some:entity')
            ->setSPProvidedID('sp:provided:id');

        $validator = new NameIdValidator();

        $validator->validateNameId($nameId);
    }

    public function test_valid_persistent_format()
    {
        $nameId = new NameID();
        $nameId->setFormat(SamlConstants::NAME_ID_FORMAT_PERSISTENT)
            ->setValue('12345678');

        $validator = new NameIdValidator();

        $validator->validateNameId($nameId);
    }

    public function test_valid_persistent_format_with_other_attributes()
    {
        $nameId = new NameID();
        $nameId->setFormat(SamlConstants::NAME_ID_FORMAT_PERSISTENT)
            ->setValue('12345678')
            ->setSPProvidedID('sp:provided:id')
            ->setSPNameQualifier('sp:name:qualifier')
            ->setNameQualifier('name:qualifier')
        ;

        $validator = new NameIdValidator();

        $validator->validateNameId($nameId);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage NameID with Persistent Format attribute MUST contain a Value that contains more than whitespace characters
     */
    public function test_invalid_persistent_format_empty()
    {
        $nameId = new NameID();
        $nameId->setFormat(SamlConstants::NAME_ID_FORMAT_PERSISTENT);

        $validator = new NameIdValidator();

        $validator->validateNameId($nameId);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage NameID with Persistent Format attribute MUST have a Value that contains no more than 256 characters
     */
    public function test_invalid_persistent_format_long()
    {
        $nameId = new NameID();
        $nameId->setFormat(SamlConstants::NAME_ID_FORMAT_PERSISTENT)
            ->setValue(str_pad('a', 260, 'x'));

        $validator = new NameIdValidator();

        $validator->validateNameId($nameId);
    }

    public function test_valid_transient_format()
    {
        $nameId = new NameID();
        $nameId->setFormat(SamlConstants::NAME_ID_FORMAT_TRANSIENT)
            ->setValue('1234567890123456');

        $validator = new NameIdValidator();

        $validator->validateNameId($nameId);
    }

    public function test_valid_transient_format_with_other_attributes()
    {
        $nameId = new NameID();
        $nameId->setFormat(SamlConstants::NAME_ID_FORMAT_TRANSIENT)
            ->setValue('1234567890123456')
            ->setSPProvidedID('sp:provided:id')
            ->setSPNameQualifier('sp:name:qualifier')
            ->setNameQualifier('name:qualifier')
        ;

        $validator = new NameIdValidator();

        $validator->validateNameId($nameId);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage NameID with Transient Format attribute MUST contain a Value that contains more than whitespace characters
     */
    public function test_invalid_transient_format_empty()
    {
        $nameId = new NameID();
        $nameId->setFormat(SamlConstants::NAME_ID_FORMAT_TRANSIENT);

        $validator = new NameIdValidator();

        $validator->validateNameId($nameId);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage NameID with Transient Format attribute MUST have a Value that contains no more than 256 characters
     */
    public function test_invalid_transient_format_long()
    {
        $nameId = new NameID();
        $nameId->setFormat(SamlConstants::NAME_ID_FORMAT_TRANSIENT)
            ->setValue(str_pad('a', 260, 'x'));

        $validator = new NameIdValidator();

        $validator->validateNameId($nameId);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage NameID '123456789012345' with Transient Format attribute MUST have a Value with at least 16 characters (the equivalent of 128 bits)
     */
    public function test_invalid_transient_format_short()
    {
        $nameId = new NameID();
        $nameId->setFormat(SamlConstants::NAME_ID_FORMAT_TRANSIENT)
            ->setValue('123456789012345');

        $validator = new NameIdValidator();

        $validator->validateNameId($nameId);
    }
}
