<?php

namespace LightSaml\Tests\Model\Xsd;

use LightSaml\Validator\Model\Xsd\XsdValidator;

class XsdValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function test_fails_on_invalid_xml()
    {
        $validator = new XsdValidator();
        $arr = $validator->validateProtocol('<a><');
        $this->assertGreaterThan(0, count($arr));
    }

    public function test_fails_on_empty_xml()
    {
        $validator = new XsdValidator();
        $arr = $validator->validateProtocol('<a><');
        $this->assertGreaterThan(0, count($arr));
    }
}
