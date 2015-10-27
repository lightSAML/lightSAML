<?php

namespace LightSaml\Tests\State\Sso;

use LightSaml\State\Sso\SsoSessionState;

class SsoSessionStateTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_without_arguments()
    {
        new SsoSessionState();
    }

    public function property_getter_setter_provider()
    {
        return [
            ['IdpEntityId'],
            ['SpEntityId'],
            ['NameId'],
            ['NameIdFormat'],
            ['SessionIndex'],
            ['FirstAuthOn', new \DateTime('2015-10-27 08:00:00')],
            ['LastAuthOn', new \DateTime('2015-10-27 10:00:00')],
            ['SessionInstant', new \DateTime('2015-10-27 06:00:00')],
        ];
    }

    /**
     * @dataProvider  property_getter_setter_provider
     *
     * @param string $property
     * @param string $value
     */
    public function test_property_getter_setter($property, $value = 'some.value')
    {
        $state = new SsoSessionState();
        $setter = sprintf('set%s', $property);
        $getter = sprintf('get%s', $property);
        $state->{$setter}($value);
        $this->assertEquals($value, $state->{$getter}());
    }

    public function test_serialization_and_deserialization()
    {
        $state = new SsoSessionState();
        $state
            ->setIdpEntityId($idp = 'IDP')
            ->setSpEntityId($sp = 'SP')
            ->setNameId($nameId = 'name.id')
            ->setNameIdFormat($nameIdFormat = 'name.id.format')
            ->setSessionIndex($sessionIndex = 'session.index')
            ->setFirstAuthOn($firstAuthOn = new \DateTime('2015-10-27 08:00:00'))
            ->setLastAuthOn($lastAuthOn = new \DateTime('2015-10-27 10:00:00'))
            ->setSessionInstant($sessionInstant = new \DateTime('2015-10-27 06:00:00'))
        ;
        $data = $state->serialize();

        $otherState = new SsoSessionState();
        $otherState->unserialize($data);

        $this->assertEquals($state->getIdpEntityId(), $otherState->getIdpEntityId());
        $this->assertEquals($state->getSpEntityId(), $otherState->getSpEntityId());
        $this->assertEquals($state->getNameId(), $otherState->getNameId());
        $this->assertEquals($state->getNameIdFormat(), $otherState->getNameIdFormat());
        $this->assertEquals($state->getSessionIndex(), $otherState->getSessionIndex());
        $this->assertEquals($state->getFirstAuthOn(), $otherState->getFirstAuthOn());
        $this->assertEquals($state->getLastAuthOn(), $otherState->getLastAuthOn());
        $this->assertEquals($state->getSessionInstant(), $otherState->getSessionInstant());
    }

    public function test_add_option()
    {
        $state = new SsoSessionState();

        $values = [
            'a' => 1,
            'bbb' => 'bbbbbbb',
            'ccccccccccc' => new \DateTime('2015-10-22 12:13:14')
        ];
        foreach ($values as $k => $v) {
            $state->addOption($k, $v);
        }

        $this->assertEquals($values, $state->getOptions());
    }

    public function test_remove_option()
    {
        $state = new SsoSessionState();

        $state->addOption('aaa', 123);
        $state->addOption('b', 'bbbbb');
        $this->assertEquals(['aaa'=>123, 'b'=>'bbbbb'], $state->getOptions());

        $state->removeOption('aaa');
        $this->assertEquals(['b'=>'bbbbb'], $state->getOptions());
    }

    public function test_has_option()
    {
        $state = new SsoSessionState();
        $this->assertFalse($state->hasOption('a'));

        $state->addOption('a', 123);
        $this->assertTrue($state->hasOption('a'));
    }
}
