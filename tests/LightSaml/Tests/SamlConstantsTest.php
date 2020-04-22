<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests;

use LightSaml\SamlConstants;

class SamlConstantsTest extends BaseTestCase
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
        return [
            ['isProtocolValid'],
            ['isNsValid'],
            ['isNameIdFormatValid'],
            ['isBindingValid'],
            ['isStatusValid'],
            ['isConfirmationMethodValid'],
            ['isAuthnContextValid'],
            ['isLogoutReasonValid'],
        ];
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
        $ret = [];
        $ref = new \ReflectionClass('\LightSaml\SamlConstants');
        $prefix = strtoupper(
            preg_replace('/([a-z])([A-Z])/', '$1_$2', $method)
        );
        $method = 'is'.$method.'Valid';

        foreach ($ref->getConstants() as $constant => $value) {
            if (0 === strpos($constant, $prefix)) {
                $ret[] = [$method, $constant];
            }
        }

        return $ret;
    }
}
