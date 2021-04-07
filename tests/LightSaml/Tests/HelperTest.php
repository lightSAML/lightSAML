<?php

namespace LightSaml\Tests\Tests;

use LightSaml\Helper;
use LightSaml\SamlConstants;
use LightSaml\Tests\BaseTestCase;

class HelperTest extends BaseTestCase
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
        $timestamps = array_merge(
            $this->timestamps,
            [
                array(1412399250, '2014-10-04T05:07:30+00:00'),
                array(1412368132, '2014-10-03T20:28:52+00:00'),
                array(1412331547, '2014-10-03T10:19:07+00:00'),
                array(1412399250, '2014-10-04T05:07:30.000+00:00'),
                array(1412368132, '2014-10-03T20:28:52.000+00:00'),
                array(1412331547, '2014-10-03T10:19:07.000+00:00'),
                array(1412399250, '2014-10-04T06:07:30+01:00'),
                array(1412368132, '2014-10-03T21:28:52+01:00'),
                array(1412331547, '2014-10-03T11:19:07+01:00'),
                array(1412399250, '2014-10-04T06:07:30.000+01:00'),
                array(1412368132, '2014-10-03T21:28:52.000+01:00'),
                array(1412331547, '2014-10-03T11:19:07.000+01:00'),
            ]
        );
        $result = array();
        foreach ($timestamps as $arr) {
            $result[] = array($arr[1], $arr[0]);
        }

        return $result;
    }

    /**
     * @param string $timestamp
     * @param string $string
     * @dataProvider timestamp2StringProvider
     */
    public function test__time_to_string($timestamp, $string)
    {
        $this->assertEquals($string, Helper::time2string($timestamp));
    }

    /**
     * @param string $value
     * @param int    $timestamp
     *
     * @dataProvider string2TimestampProvider
     */
    public function test__get_timestamp_from_value_with_string($value, $timestamp)
    {
        $this->assertEquals($timestamp, Helper::getTimestampFromValue($value));
    }

    /**
     * @param string $value
     * @param int    $timestamp
     *
     * @dataProvider string2TimestampProvider
     */
    public function test__get_timestamp_from_value_with_date_time($value, $timestamp)
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
    public function test__get_timestamp_from_value_with_int($value, $timestamp)
    {
        $this->assertEquals($timestamp, Helper::getTimestampFromValue($timestamp));
    }

    public function test__get_timestamp_from_value_with_invalid_value()
    {
        $this->expectException(\InvalidArgumentException::class);
        Helper::getTimestampFromValue(array());
    }

    public function test__generate_random_bytes_length()
    {
        $random = Helper::generateRandomBytes(10);
        $this->assertEquals(10, strlen($random));

        $random = Helper::generateRandomBytes(16);
        $this->assertEquals(16, strlen($random));

        $random = Helper::generateRandomBytes(32);
        $this->assertEquals(32, strlen($random));
    }

    public function test__generate_random_bytes_error_on_invalid_length()
    {
        $this->expectException(\InvalidArgumentException::class);
        Helper::generateRandomBytes('');
    }

    public function test__generate_id()
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

    public function test__validate_id_string_returns_true_for_valid_string()
    {
        $this->assertTrue(Helper::validateIdString('1234567890123456'));
        $this->assertTrue(Helper::validateIdString('12345678901234567890'));
    }

    public function test__validate_id_string_returns_false_for_non_string()
    {
        $this->assertFalse(Helper::validateIdString(1234567890123456));
        $this->assertFalse(Helper::validateIdString(array()));
    }

    public function test__validate_id_string_returns_false_for_short_string()
    {
        $this->assertFalse(Helper::validateIdString(''));
        $this->assertFalse(Helper::validateIdString('abc'));
        $this->assertFalse(Helper::validateIdString('123456789012345'));
    }

    public function test__validate_required_string_returns_true_for_non_empty_string()
    {
        $this->assertTrue(Helper::validateRequiredString('1'));
        $this->assertTrue(Helper::validateRequiredString('123'));
        $this->assertTrue(Helper::validateRequiredString('123456789'));
    }

    public function test__validate_required_string_returns_false_for_empty_string()
    {
        $this->assertFalse(Helper::validateRequiredString(''));
    }

    public function test__validate_required_string_returns_false_for_null()
    {
        $this->assertFalse(Helper::validateRequiredString(null));
    }

    public function test__validate_required_string_returns_false_for_non_string()
    {
        $this->assertFalse(Helper::validateRequiredString(123));
        $this->assertFalse(Helper::validateRequiredString(array()));
    }

    public function test__validate_optional_string_returns_true_for_null()
    {
        $this->assertTrue(Helper::validateOptionalString(null));
    }

    public function test__validate_optional_string_returns_true_for_non_empty_string()
    {
        $this->assertTrue(Helper::validateOptionalString('1'));
        $this->assertTrue(Helper::validateOptionalString('1234'));
    }

    public function test__validate_optional_string_returns_false_for_empty_string()
    {
        $this->assertFalse(Helper::validateOptionalString(''));
    }

    public function test__validate_optional_string_returns_false_for_non_string()
    {
        $this->assertFalse(Helper::validateOptionalString(123));
        $this->assertFalse(Helper::validateOptionalString(array()));
    }

    public function test__validate_well_formed_uri_string_returns_false_for_empty_string()
    {
        $this->assertFalse(Helper::validateWellFormedUriString(''));
    }

    public function test__validate_well_formed_uri_string_returns_false_for_null()
    {
        $this->assertFalse(Helper::validateWellFormedUriString(null));
    }

    public function test__validate_well_formed_uri_string_returns_false_for_too_big_string()
    {
        $str = str_pad('', 67000, 'x');
        $this->assertFalse(Helper::validateWellFormedUriString($str));
    }

    public function test__validate_well_formed_uri_string_returns_false_for_string_with_spaces()
    {
        $this->assertFalse(Helper::validateWellFormedUriString('123 456 789'));
    }

    public function test__validate_well_formed_uri_string_returns_false_for_string_without_scheme()
    {
        $this->assertFalse(Helper::validateWellFormedUriString('example.com'));
        $this->assertFalse(Helper::validateWellFormedUriString(':example.com'));
        $this->assertFalse(Helper::validateWellFormedUriString('//:example.com'));
    }

    public function test__validate_well_formed_uri_string_returns_false_for_string_with_invalid_scheme()
    {
        $this->assertFalse(Helper::validateWellFormedUriString('a=b:example.com'));
        $this->assertFalse(Helper::validateWellFormedUriString('a b:example.com'));
        $this->assertFalse(Helper::validateWellFormedUriString('a&b:example.com'));
    }

    public function test__validate_well_formed_uri_string_returns_false_for_valid_string()
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
    public function test__validate_not_before($notBefore, $now, $allowedSecondsSkew, $expected)
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
    public function test__validate_not_on_or_after($notOnOrAfter, $now, $allowedSecondsSkew, $expected)
    {
        $this->assertEquals($expected, Helper::validateNotOnOrAfter($notOnOrAfter, $now, $allowedSecondsSkew));
    }
}
