<?php

namespace LightSaml\Tests\Tests;

use LightSaml\SamlConstants;

class SamlConstantsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider methodsProvider
     */
    public function test__is_not_valid($method)
    {
        $this->assertFalse(SamlConstants::$method('Nonsense'));
    }

    /**
     * @dataProvider constantsProvider
     */
    public function test__is_valid_method($method, $constant)
    {
        $value = constant('\LightSaml\SamlConstants::'.$constant);
        $this->assertTrue(SamlConstants::$method($value));
    }

    public function methodsProvider()
    {
        return array(
            array('isProtocolValid'),
            array('isNsValid'),
            array('isNameIdFormatValid'),
            array('isBindingValid'),
            array('isStatusValid'),
            array('isConfirmationMethodValid'),
            array('isAuthnContextValid'),
            array('isLogoutReasonValid'),
        );
    }

    public function constantsProvider()
    {
        return array_merge(
            $this->getConstants('Protocol'),
            $this->getConstants('Ns'),
            $this->getConstants('NameIdFormat'),
            $this->getConstants('Binding'),
            $this->getConstants('Status'),
            $this->getConstants('ConfirmationMethod'),
            $this->getConstants('AuthnContext'),
            $this->getConstants('LogoutReason')
        );
    }

    public function getConstants($method)
    {
        $ret = array();
        $ref = new \ReflectionClass('\LightSaml\SamlConstants');
        $prefix = strtoupper(
            preg_replace('/([a-z])([A-Z])/', '$1_$2', $method)
        );
        $method = 'is'.$method.'Valid';

        foreach ($ref->getConstants() as $constant => $value) {
            if (strpos($constant, $prefix) === 0) {
                $ret[] = array($method, $constant);
            }
        }

        return $ret;
    }
}
