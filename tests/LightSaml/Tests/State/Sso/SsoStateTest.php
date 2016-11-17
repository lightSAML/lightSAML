<?php

namespace LightSaml\Tests\State\Sso;

use LightSaml\Meta\ParameterBag;
use LightSaml\State\Sso\SsoSessionState;
use LightSaml\State\Sso\SsoState;

class SsoStateTest extends \PHPUnit_Framework_TestCase
{
    public function test_can_be_constructed_without_arguments()
    {
        new SsoState();
    }

    public function property_getter_setter_provider()
    {
        return [
            ['LocalSessionId']
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
        $state = new SsoState();
        $setter = sprintf('set%s', $property);
        $getter = sprintf('get%s', $property);
        $state->{$setter}($value);
        $this->assertEquals($value, $state->{$getter}());
    }

    public function test_adds_sso_session_state()
    {
        $state = new SsoState();

        $session1 = new SsoSessionState();
        $session1->setIdpEntityId($session1Idp = 'http://idp-1.com');
        $session1->setSpEntityId($session1sp = 'http://sp-1.com');
        $state->addSsoSession($session1);

        $this->assertTrue(is_array($state->getSsoSessions()));
        $this->assertCount(1, $state->getSsoSessions());

        $session2 = new SsoSessionState();
        $session2->setIdpEntityId($session1Idp = 'http://idp-2.com');
        $session2->setSpEntityId($session1sp = 'http://sp-2.com');
        $state->addSsoSession($session1);

        $this->assertCount(2, $state->getSsoSessions());
    }

    public function test_filter_by_idp()
    {
        $arrIdp = [
            'http://idp-1.com',
            'http://idp-2.com',
            'http://idp-3.com'
        ];
        $arrSp = [
            'http://sp-1.com',
            'http://sp-2.com',
            'http://sp-3.com'
        ];

        $state = $this->buildAllStateCombinations($arrIdp, $arrSp);
        $allSessions = $state->getSsoSessions();

        $arr = $state->filter($arrIdp[0], null, null, null, null);

        foreach ($arr as $session) {
            $this->assertEquals($arrIdp[0], $session->getIdpEntityId());
        }
        $this->assertTrue(is_array($arr));
        $this->assertCount(3, $arr);
        $this->assertSame($allSessions[0], $arr[0]);
        $this->assertSame($allSessions[1], $arr[1]);
        $this->assertSame($allSessions[2], $arr[2]);

        $arr = $state->filter($arrIdp[1], null, null, null, null);

        $this->assertTrue(is_array($arr));
        $this->assertCount(3, $arr);
        $this->assertSame($allSessions[3], $arr[0]);
        $this->assertSame($allSessions[4], $arr[1]);
        $this->assertSame($allSessions[5], $arr[2]);

        $arr = $state->filter($arrIdp[2], null, null, null, null);

        $this->assertTrue(is_array($arr));
        $this->assertCount(3, $arr);
        $this->assertSame($allSessions[6], $arr[0]);
        $this->assertSame($allSessions[7], $arr[1]);
        $this->assertSame($allSessions[8], $arr[2]);
    }

    public function test_filter_by_sp()
    {
        $arrIdp = [
            'http://idp-1.com',
            'http://idp-2.com',
            'http://idp-3.com'
        ];
        $arrSp = [
            'http://sp-1.com',
            'http://sp-2.com',
            'http://sp-3.com'
        ];

        $state = $this->buildAllStateCombinations($arrIdp, $arrSp);
        $allSessions = $state->getSsoSessions();

        $arr = $state->filter(null, $arrSp[0], null, null, null);

        foreach ($arr as $session) {
            $this->assertEquals($arrSp[0], $session->getSpEntityId());
        }
        $this->assertTrue(is_array($arr));
        $this->assertCount(3, $arr);
        $this->assertSame($allSessions[0], $arr[0]);
        $this->assertSame($allSessions[3], $arr[1]);
        $this->assertSame($allSessions[6], $arr[2]);

        $arr = $state->filter(null, $arrSp[0], null, null, null);

        foreach ($arr as $session) {
            $this->assertEquals($arrSp[0], $session->getSpEntityId());
        }
        $this->assertTrue(is_array($arr));
        $this->assertCount(3, $arr);
        $this->assertSame($allSessions[0], $arr[0]);
        $this->assertSame($allSessions[3], $arr[1]);
        $this->assertSame($allSessions[6], $arr[2]);
    }

    public function test_serialize_deserialize()
    {
        $arrIdp = [
            'http://idp-1.com',
            'http://idp-2.com',
            'http://idp-3.com'
        ];
        $arrSp = [
            'http://sp-1.com',
            'http://sp-2.com',
            'http://sp-3.com'
        ];

        $state = $this->buildAllStateCombinations($arrIdp, $arrSp);
        $state->addOption('a', 1);
        $state->addOption('b', 2);
        $sessions = $state->getSsoSessions();

        $data = $state->serialize();

        $otherState = new SsoState();
        $otherState->unserialize($data);
        $otherSessions = $otherState->getSsoSessions();

        $this->assertEquals($state->getOptions(), $otherState->getOptions());
        $this->assertEquals(count($sessions), count($otherSessions));
        foreach ($sessions as $k => $session) {
            $this->assertEquals($session->getIdpEntityId(), $otherSessions[$k]->getIdpEntityId());
            $this->assertEquals($session->getSpEntityId(), $otherSessions[$k]->getSpEntityId());
        }
    }

    /**
     * @deprecated Options methods will be removed in 2.0
     */
    public function test_options()
    {
        $state = new SsoState();
        $this->assertFalse($state->hasOption('a'));
        $this->assertEquals([], $state->getOptions());

        $state->addOption('a', 1);
        $this->assertTrue($state->hasOption('a'));
        $this->assertEquals(['a'=>1], $state->getOptions());

        $state->removeOption('a');
        $this->assertFalse($state->hasOption('a'));
        $this->assertEquals([], $state->getOptions());
    }

    public function test_set_sso_state_sessions()
    {
        $state = new SsoState();
        $state->setSsoSessions([$session1 = new SsoSessionState(), $session2 = new SsoSessionState()]);

        $sessions = $state->getSsoSessions();
        $this->assertCount(2, $sessions);
        $this->assertSame($session1, $sessions[0]);
        $this->assertSame($session2, $sessions[1]);
    }

    public function test_has_parameters()
    {
        $state = new SsoState();
        $this->assertInstanceOf(ParameterBag::class, $state->getParameters());
    }

    public function test_serialization()
    {
        $state = new SsoState();
        $state->setLocalSessionId('local-id');
        $state->getParameters()->add(['a' => 'aaa', 'b' => 2, 'c' => [1,2,3]]);
        $state->addSsoSession($session1 = new SsoSessionState());
        $session1->setIdpEntityId($idp1 = 'idp1');
        $state->addSsoSession($session2 = new SsoSessionState());
        $session2->setIdpEntityId($idp2 = 'idp2');

        /** @var SsoState $other */
        $other = unserialize(serialize($state));

        $this->assertEquals($state->getLocalSessionId(), $other->getLocalSessionId());
        $this->assertInstanceOf(ParameterBag::class, $other->getParameters());
        $this->assertEquals($state->getParameters()->all(), $other->getParameters()->all());
        $this->assertEquals(count($state->getSsoSessions()), count($other->getSsoSessions()));
    }

    public function test_modify()
    {
        $state = new SsoState();
        $state->addSsoSession($session1 = new SsoSessionState());
        $session1->setIdpEntityId('idp-1');
        $state->addSsoSession($session2 = new SsoSessionState());
        $session2->setIdpEntityId('idp-2');

        $state->modify(function (SsoSessionState $session) use ($session1) {
            return $session->getIdpEntityId() != $session1->getIdpEntityId();
        });

        $sessions = $state->getSsoSessions();
        $this->assertCount(1, $sessions);
        $this->assertEquals($session2->getIdpEntityId(), $sessions[0]->getIdpEntityId());
    }

    /**
     * @param array $arrIdp
     * @param array $arrSp
     *
     * @return SsoState
     */
    private function buildAllStateCombinations(array $arrIdp, array $arrSp)
    {
        $state = new SsoState();

        foreach ($arrIdp as $idp) {
            foreach ($arrSp as $sp) {
                $state->addSsoSession($session1 = (new SsoSessionState())
                    ->setIdpEntityId($idp)
                    ->setSpEntityId($sp));
            }
        }

        return $state;
    }
}
