<?php

namespace LightSaml\Tests\Model\Assertion;

use LightSaml\Model\Assertion\AudienceRestriction;
use LightSaml\Model\Assertion\Conditions;
use LightSaml\Model\Assertion\OneTimeUse;
use LightSaml\Model\Assertion\ProxyRestriction;

class ConditionsTest extends \PHPUnit_Framework_TestCase
{
    public function test_get_all_audience_restrictions()
    {
        $conditions = new Conditions();
        $conditions->addItem($expected1 = new AudienceRestriction());
        $conditions->addItem(new OneTimeUse());
        $conditions->addItem($expected2 = new AudienceRestriction());
        $conditions->addItem(new ProxyRestriction());

        $arr = $conditions->getAllAudienceRestrictions();

        $this->assertCount(2, $arr);
        $this->assertSame($expected1, $arr[0]);
        $this->assertSame($expected2, $arr[1]);
    }

    public function test_get_first_audience_restrictions()
    {
        $conditions = new Conditions();
        $conditions->addItem($expected = new AudienceRestriction());
        $conditions->addItem(new OneTimeUse());
        $conditions->addItem(new AudienceRestriction());
        $conditions->addItem(new ProxyRestriction());

        $actual = $conditions->getFirstAudienceRestriction();

        $this->assertSame($expected, $actual);
    }

    public function test_get_all_one_time_uses()
    {
        $conditions = new Conditions();
        $conditions->addItem(new AudienceRestriction());
        $conditions->addItem($expected1 = new OneTimeUse());
        $conditions->addItem(new AudienceRestriction());
        $conditions->addItem(new ProxyRestriction());
        $conditions->addItem($expected2 = new OneTimeUse());

        $arr = $conditions->getAllOneTimeUses();

        $this->assertCount(2, $arr);
        $this->assertSame($expected1, $arr[0]);
        $this->assertSame($expected2, $arr[1]);
    }

    public function test_get_first_one_time_use()
    {
        $conditions = new Conditions();
        $conditions->addItem(new AudienceRestriction());
        $conditions->addItem($expected = new OneTimeUse());
        $conditions->addItem(new AudienceRestriction());
        $conditions->addItem(new ProxyRestriction());
        $conditions->addItem(new OneTimeUse());

        $actual = $conditions->getFirstOneTimeUse();

        $this->assertSame($expected, $actual);
    }

    public function test_get_all_proxy_restrictions()
    {
        $conditions = new Conditions();
        $conditions->addItem(new AudienceRestriction());
        $conditions->addItem($expected1 = new ProxyRestriction());
        $conditions->addItem(new OneTimeUse());
        $conditions->addItem(new AudienceRestriction());
        $conditions->addItem($expected2 = new ProxyRestriction());

        $arr = $conditions->getAllProxyRestrictions();

        $this->assertCount(2, $arr);
        $this->assertSame($expected1, $arr[0]);
        $this->assertSame($expected2, $arr[1]);
    }

    public function test_get_first_proxy_restriction()
    {
        $conditions = new Conditions();
        $conditions->addItem(new AudienceRestriction());
        $conditions->addItem($expected = new ProxyRestriction());
        $conditions->addItem(new OneTimeUse());
        $conditions->addItem(new ProxyRestriction());
        $conditions->addItem(new AudienceRestriction());

        $actual = $conditions->getFirstProxyRestriction();

        $this->assertSame($expected, $actual);
    }
}
