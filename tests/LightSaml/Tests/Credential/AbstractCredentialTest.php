<?php

namespace LightSaml\Tests\Credential;

use LightSaml\Credential\UsageType;

class AbstractCredentialTest extends \PHPUnit_Framework_TestCase
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

        $this->assertEquals(array(), $credential->getKeyNames());

        $credential->addKeyName($keyName1 = 'foo');
        $this->assertEquals(array($keyName1), $credential->getKeyNames());

        $credential->addKeyName($keyName2 = 'bar');
        $this->assertEquals(array($keyName1, $keyName2), $credential->getKeyNames());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Credential\AbstractCredential
     */
    private function getAbstractCredentialMock()
    {
        return $this->getMockForAbstractClass('LightSaml\Credential\AbstractCredential');
    }
}
