<?php

namespace LightSaml\Tests\Criteria;

use LightSaml\Criteria\CriteriaSet;
use LightSaml\Resolver\Endpoint\Criteria\IndexCriteria;
use LightSaml\Tests\TestHelper;

class CriteriaSetTest extends \PHPUnit_Framework_TestCase
{
    public function test_add_all()
    {
        $criteriaSet = new CriteriaSet();
        $criteriaSet->addAll(new CriteriaSet([
            $criteria1 = TestHelper::getCriteriaMock($this),
            $criteria2 = TestHelper::getCriteriaMock($this),
        ]));

        $all = $criteriaSet->all();

        $this->assertCount(2, $all);
        $this->assertSame($criteria1, $all[0]);
        $this->assertSame($criteria2, $all[1]);
    }

    public function test_add_if_none()
    {
        $criteriaSet = new CriteriaSet();

        $criteriaSet->addIfNone(new IndexCriteria(1));
        $criteriaSet->addIfNone(new IndexCriteria(1));

        $all = $criteriaSet->all();

        $this->assertCount(1, $all);
    }

    public function test_add_if()
    {
        $criteriaSet = new CriteriaSet();

        $criteriaSet->addIf(false, function () {
            return TestHelper::getCriteriaMock($this);

        });
        $criteriaSet->addIf(true, function () {
            return TestHelper::getCriteriaMock($this);

        });

        $all = $criteriaSet->all();

        $this->assertCount(1, $all);
    }
}
