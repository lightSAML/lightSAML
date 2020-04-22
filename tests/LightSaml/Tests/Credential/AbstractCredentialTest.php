<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests\Credential;

use LightSaml\Credential\UsageType;
use LightSaml\Tests\BaseTestCase;

class AbstractCredentialTest extends BaseTestCase
{
    public function test__set_get_entity_id()
    {
        $credential = $this->getAbstractCredentialMock();
        $credential->setEntityId($expectedValue = 'entity-foo');

        $this->assertEquals($expectedValue, $credential->getEntityId());
    }

    public function test__set_get_usage_type()
    {
        $credential = $this->getAbstractCredentialMock();
        $credential->setUsageType($expectedValue = UsageType::ENCRYPTION);

        $this->assertEquals($expectedValue, $credential->getUsageType());
    }

    public function test__set_get_secret_key()
    {
        $credential = $this->getAbstractCredentialMock();
        $credential->setSecretKey($expectedValue = '123123123');

        $this->assertEquals($expectedValue, $credential->getSecretKey());
    }

    public function test__creates_credential_context_on_construct()
    {
        $credential = $this->getAbstractCredentialMock();

        $this->assertNotNull($credential->getCredentialContext());
    }

    public function test__add_key_name()
    {
        $credential = $this->getAbstractCredentialMock();

        $this->assertEquals([], $credential->getKeyNames());

        $credential->addKeyName($keyName1 = 'foo');
        $this->assertEquals([$keyName1], $credential->getKeyNames());

        $credential->addKeyName($keyName2 = 'bar');
        $this->assertEquals([$keyName1, $keyName2], $credential->getKeyNames());
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\LightSaml\Credential\AbstractCredential
     */
    private function getAbstractCredentialMock()
    {
        return $this->getMockForAbstractClass('LightSaml\Credential\AbstractCredential');
    }
}
