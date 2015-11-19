<?php

namespace LightSaml\Tests\Store\EntityDescriptor;

use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Store\EntityDescriptor\CompositeEntityDescriptorStore;
use LightSaml\Tests\TestHelper;

class CompositeEntityDescriptorStoreTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_without_arguments()
    {
        new CompositeEntityDescriptorStore();
    }

    public function test_constructs_with_array_of_entity_descriptor_stores()
    {
        new CompositeEntityDescriptorStore([
            TestHelper::getEntityDescriptorStoreMock($this),
            TestHelper::getEntityDescriptorStoreMock($this),
        ]);
    }

    public function test_entity_descriptor_store_can_be_added()
    {
        $composite = new CompositeEntityDescriptorStore();
        $composite->add(TestHelper::getEntityDescriptorStoreMock($this));
    }

    public function test_get_returns_value_given_by_child_store()
    {
        $composite = new CompositeEntityDescriptorStore([
            $child1 = TestHelper::getEntityDescriptorStoreMock($this),
            $child2 = TestHelper::getEntityDescriptorStoreMock($this),
            $child3 = TestHelper::getEntityDescriptorStoreMock($this),
        ]);

        $entityId = 'http://entity.id';
        $child1->expects($this->once())->method('get')->with($entityId)->willReturn(null);
        $child2->expects($this->once())->method('get')->with($entityId)->willReturn($expected = new EntityDescriptor());
        $child3->expects($this->never())->method('get');

        $actual = $composite->get($entityId);
        $this->assertSame($expected, $actual);
    }

    public function test_has_return_true_if_any_child_returns_true()
    {
        $composite = new CompositeEntityDescriptorStore([
            $child1 = TestHelper::getEntityDescriptorStoreMock($this),
            $child2 = TestHelper::getEntityDescriptorStoreMock($this),
            $child3 = TestHelper::getEntityDescriptorStoreMock($this),
        ]);

        $entityId = 'http://entity.id';
        $child1->expects($this->once())->method('has')->with($entityId)->willReturn(false);
        $child2->expects($this->once())->method('has')->with($entityId)->willReturn(true);
        $child3->expects($this->never())->method('has');

        $this->assertTrue($composite->has($entityId));
    }

    public function test_all_returns_union_of_all_children_results()
    {
        $composite = new CompositeEntityDescriptorStore([
            $child1 = TestHelper::getEntityDescriptorStoreMock($this),
            $child2 = TestHelper::getEntityDescriptorStoreMock($this),
            $child3 = TestHelper::getEntityDescriptorStoreMock($this),
        ]);

        $child1->expects($this->once())->method('all')->willReturn([$ed1 = new EntityDescriptor()]);
        $child2->expects($this->once())->method('all')->willReturn([$ed2 = new EntityDescriptor(), $ed3 = new EntityDescriptor()]);
        $child3->expects($this->once())->method('all')->willReturn([]);

        $all = $composite->all();

        $this->assertCount(3, $all);
        $this->assertSame($ed1, $all[0]);
        $this->assertSame($ed2, $all[1]);
        $this->assertSame($ed3, $all[2]);
    }
}
