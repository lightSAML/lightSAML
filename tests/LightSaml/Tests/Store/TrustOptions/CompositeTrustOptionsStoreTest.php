<?php

namespace LightSaml\Tests\Store\TrustOptions;

use LightSaml\Meta\TrustOptions\TrustOptions;
use LightSaml\Store\TrustOptions\CompositeTrustOptionsStore;
use LightSaml\Store\TrustOptions\TrustOptionsStoreInterface;

class CompositeTrustOptionsStoreTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_without_arguments()
    {
        new CompositeTrustOptionsStore();
    }

    public function test_constructs_wit_array_of_stores()
    {
        new CompositeTrustOptionsStore([$this->getTrustOptionsStoreMock(), $this->getTrustOptionsStoreMock()]);
    }
    
    public function test_can_add_stores()
    {
        $composite = new CompositeTrustOptionsStore();
        $composite->add($this->getTrustOptionsStoreMock());
    }

    public function test_get_calls_each_store()
    {
        $expectedEntityId = 'id';
        $composite = new CompositeTrustOptionsStore();
        $store = $this->getTrustOptionsStoreMock();
        $store->expects($this->once())
            ->method('get')
            ->with($expectedEntityId)
            ->willReturn(null);
        $composite->add($store);

        $result = $composite->get($expectedEntityId);

        $this->assertNull($result);
    }

    public function test_get_returns_first_result()
    {
        $expectedEntityId = 'id';
        $expectedTrustOptions = new TrustOptions();
        $composite = new CompositeTrustOptionsStore();

        $composite->add($this->getTrustOptionsStoreMock());

        $store = $this->getTrustOptionsStoreMock();
        $store->expects($this->once())
            ->method('get')
            ->with($expectedEntityId)
            ->willReturn($expectedTrustOptions);
        $composite->add($store);

        $composite->add($this->getTrustOptionsStoreMock());

        $result = $composite->get($expectedEntityId);

        $this->assertSame($expectedTrustOptions, $result);
    }

    public function test_has_calls_each_store()
    {
        $expectedEntityId = 'id';
        $composite = new CompositeTrustOptionsStore();
        $store = $this->getTrustOptionsStoreMock();
        $store->expects($this->once())
            ->method('has')
            ->with($expectedEntityId)
            ->willReturn(null);
        $composite->add($store);

        $result = $composite->has($expectedEntityId);

        $this->assertFalse($result);
    }

    public function test_has_returns_true_on_first_true()
    {
        $expectedEntityId = 'id';
        $expectedTrustOptions = new TrustOptions();
        $composite = new CompositeTrustOptionsStore();

        $composite->add($this->getTrustOptionsStoreMock());

        $store = $this->getTrustOptionsStoreMock();
        $store->expects($this->once())
            ->method('has')
            ->with($expectedEntityId)
            ->willReturn($expectedTrustOptions);
        $composite->add($store);

        $composite->add($this->getTrustOptionsStoreMock());

        $result = $composite->has($expectedEntityId);

        $this->assertTrue($result);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|TrustOptionsStoreInterface
     */
    private function getTrustOptionsStoreMock()
    {
        return $this->getMockBuilder(TrustOptionsStoreInterface::class)->getMock();
    }
}
