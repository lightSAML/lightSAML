<?php

namespace LightSaml\Tests\Tests;

use LightSaml\Helper;
use LightSaml\SamlConstants;

class HelperTest extends \PHPUnit_Framework_TestCase
{
    protected $timestamps = array(
        array(1412399250, '2014-10-04T05:07:30Z'),
        array(1412368132, '2014-10-03T20:28:52Z'),
        array(1412331547, '2014-10-03T10:19:07Z'),
    );

    /**
     * @return array
     */
    public function timestamp2StringProvider()
    {
        return $this->timestamps;
    }

    /**
     * @return array
     */
    public function string2TimestampProvider()
    {
        $result = array();
        foreach ($this->timestamps as $arr) {
            $result[] = array($arr[1], $arr[0]);
        }

        return $result;
    }

    /**
     * @param string $timestamp
     * @param string $string
     * @dataProvider timestamp2StringProvider
     */
    public function testTime2String($timestamp, $string)
    {
        $this->assertEquals($string, Helper::time2string($timestamp));
    }

    /**
     * @param string $value
     * @param int    $timestamp
     *
     * @dataProvider string2TimestampProvider
     */
    public function testGetTimestampFromValueWithString($value, $timestamp)
    {
        $this->assertEquals($timestamp, Helper::getTimestampFromValue($value));
    }

    /**
     * @param string $value
     * @param int    $timestamp
     *
     * @dataProvider string2TimestampProvider
     */
    public function testGetTimestampFromValueWithDateTime($value, $timestamp)
    {
        $dt = new \DateTime('@'.$timestamp);
        $this->assertEquals($timestamp, Helper::getTimestampFromValue($dt));
    }

    /**
     * @param string $value
     * @param int    $timestamp
     *
     * @dataProvider string2TimestampProvider
     */
    public function testGetTimestampFromValueWithInt($value, $timestamp)
    {
        $this->assertEquals($timestamp, Helper::getTimestampFromValue($timestamp));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetTimestampFromValueWithInvalidValue()
    {
        Helper::getTimestampFromValue(array());
    }

    public function testGenerateRandomBytesLength()
    {
        $random = Helper::generateRandomBytes(10);
        $this->assertEquals(10, strlen($random));

        $random = Helper::generateRandomBytes(16);
        $this->assertEquals(16, strlen($random));

        $random = Helper::generateRandomBytes(32);
        $this->assertEquals(32, strlen($random));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGenerateRandomBytesErrorOnInvalidLength()
    {
        Helper::generateRandomBytes('');
    }

    public function testGenerateId()
    {
        $id = Helper::generateID();
        $this->assertStringStartsWith('_', $id);
        $this->assertEquals(43, strlen($id));

        $arr = array();
        for ($i = 0; $i<strlen($id); $i++) {
            $ch = $id[$i];
            $arr[$ch] = true;
        }
        $this->assertGreaterThan(8, count($arr));
    }

    public function testValidateIdStringReturnsTrueForValidString()
    {
        $this->assertTrue(Helper::validateIdString('1234567890123456'));
        $this->assertTrue(Helper::validateIdString('12345678901234567890'));
    }

    public function testValidateIdStringReturnsFalseForNonString()
    {
        $this->assertFalse(Helper::validateIdString(1234567890123456));
        $this->assertFalse(Helper::validateIdString(array()));
    }

    public function testValidateIdStringReturnsFalseForShortString()
    {
        $this->assertFalse(Helper::validateIdString(''));
        $this->assertFalse(Helper::validateIdString('abc'));
        $this->assertFalse(Helper::validateIdString('123456789012345'));
    }

    public function testValidateRequiredStringReturnsTrueForNonEmptyString()
    {
        $this->assertTrue(Helper::validateRequiredString('1'));
        $this->assertTrue(Helper::validateRequiredString('123'));
        $this->assertTrue(Helper::validateRequiredString('123456789'));
    }

    public function testValidateRequiredStringReturnsFalseForEmptyString()
    {
        $this->assertFalse(Helper::validateRequiredString(''));
    }

    public function testValidateRequiredStringReturnsFalseForNull()
    {
        $this->assertFalse(Helper::validateRequiredString(null));
    }

    public function testValidateRequiredStringReturnsFalseForNonString()
    {
        $this->assertFalse(Helper::validateRequiredString(123));
        $this->assertFalse(Helper::validateRequiredString(array()));
    }

    public function testValidateOptionalStringReturnsTrueForNull()
    {
        $this->assertTrue(Helper::validateOptionalString(null));
    }

    public function testValidateOptionalStringReturnsTrueForNonEmptyString()
    {
        $this->assertTrue(Helper::validateOptionalString('1'));
        $this->assertTrue(Helper::validateOptionalString('1234'));
    }

    public function testValidateOptionalStringReturnsFalseForEmptyString()
    {
        $this->assertFalse(Helper::validateOptionalString(''));
    }

    public function testValidateOptionalStringReturnsFalseForNonString()
    {
        $this->assertFalse(Helper::validateOptionalString(123));
        $this->assertFalse(Helper::validateOptionalString(array()));
    }

    public function testValidateWellFormedUriStringReturnsFalseForEmptyString()
    {
        $this->assertFalse(Helper::validateWellFormedUriString(''));
    }

    public function testValidateWellFormedUriStringReturnsFalseForNull()
    {
        $this->assertFalse(Helper::validateWellFormedUriString(null));
    }

    public function testValidateWellFormedUriStringReturnsFalseForTooBigString()
    {
        $str = str_pad('', 67000, 'x');
        $this->assertFalse(Helper::validateWellFormedUriString($str));
    }

    public function testValidateWellFormedUriStringReturnsFalseForStringWithSpaces()
    {
        $this->assertFalse(Helper::validateWellFormedUriString('123 456 789'));
    }

    public function testValidateWellFormedUriStringReturnsFalseForStringWithoutScheme()
    {
        $this->assertFalse(Helper::validateWellFormedUriString('example.com'));
        $this->assertFalse(Helper::validateWellFormedUriString(':example.com'));
        $this->assertFalse(Helper::validateWellFormedUriString('//:example.com'));
    }

    public function testValidateWellFormedUriStringReturnsFalseForStringWithInvalidScheme()
    {
        $this->assertFalse(Helper::validateWellFormedUriString('a=b:example.com'));
        $this->assertFalse(Helper::validateWellFormedUriString('a b:example.com'));
        $this->assertFalse(Helper::validateWellFormedUriString('a&b:example.com'));
    }

    public function testValidateWellFormedUriStringReturnsFalseForValidString()
    {
        $this->assertTrue(Helper::validateWellFormedUriString('http://example.com'));
        $this->assertTrue(Helper::validateWellFormedUriString(SamlConstants::NS_ASSERTION));
        $this->assertTrue(Helper::validateWellFormedUriString(SamlConstants::PROTOCOL_SAML2));
        $this->assertTrue(Helper::validateWellFormedUriString(SamlConstants::NAME_ID_FORMAT_EMAIL));
        $this->assertTrue(Helper::validateWellFormedUriString(SamlConstants::BINDING_SAML2_HTTP_REDIRECT));
        $this->assertTrue(Helper::validateWellFormedUriString(SamlConstants::STATUS_SUCCESS));
        $this->assertTrue(Helper::validateWellFormedUriString(SamlConstants::AUTHN_CONTEXT_PASSWORD));
    }

    public function notBeforeProvider()
    {
        return array(
            array(1000, 900, 10, false),
            array(1000, 1100, 10, true),
        );
    }

    /**
     * @dataProvider notBeforeProvider
     */
    public function testValidateNotBefore($notBefore, $now, $allowedSecondsSkew, $expected)
    {
        $this->assertEquals($expected, Helper::validateNotBefore($notBefore, $now, $allowedSecondsSkew));
    }

    public function notOnOrAfterProvider()
    {
        return array(
            array(1000, 900, 10, true),
            array(1000, 1100, 10, false),
        );
    }

    /**
     * @dataProvider notOnOrAfterProvider
     */
    public function testValidateNotOnOrAfter($notOnOrAfter, $now, $allowedSecondsSkew, $expected)
    {
        $this->assertEquals($expected, Helper::validateNotOnOrAfter($notOnOrAfter, $now, $allowedSecondsSkew));
    }
}
