<?php

namespace LightSaml\Tests\Functional\Store\EntityDescriptor;

use LightSaml\Store\EntityDescriptor\FileEntityDescriptorStore;

class FileEntityDescriptorStoreTest extends \PHPUnit_Framework_TestCase
{
    public function test_loads_entity_descriptor_file()
    {
        $store = new FileEntityDescriptorStore(__DIR__.'/../../../../../../resources/sample/EntityDescriptor/idp-ed.xml');
        $ed = $store->get('https://sts.windows.net/554fadfe-f04f-4975-90cb-ddc8b147aaa2/');
        $this->assertEquals('_127800fe-39ac-46ad-b073-6fb6106797a0', $ed->getID());
    }

    public function test_has_returns_true_if_entity_id_matches()
    {
        $store = new FileEntityDescriptorStore(__DIR__.'/../../../../../../resources/sample/EntityDescriptor/idp-ed.xml');
        $this->assertTrue($store->has('https://sts.windows.net/554fadfe-f04f-4975-90cb-ddc8b147aaa2/'));
    }

    public function test_all_returns_array_of_single_loaded_entity_descriptor()
    {
        $store = new FileEntityDescriptorStore(__DIR__.'/../../../../../../resources/sample/EntityDescriptor/idp-ed.xml');
        $all = $store->all();
        $this->assertCount(1, $all);
        $this->assertEquals('_127800fe-39ac-46ad-b073-6fb6106797a0', $all[0]->getID());
    }

    public function test_loads_entities_descriptor_file()
    {
        $store = new FileEntityDescriptorStore(__DIR__.'/../../../../../../resources/sample/EntitiesDescriptor/testshib-providers.xml');
        $ed = $store->get('https://idp.testshib.org/idp/shibboleth');
        $this->assertNotNull($ed);
    }

    public function test_all_returns_all_entities_descriptor_items()
    {
        $store = new FileEntityDescriptorStore(__DIR__.'/../../../../../../resources/sample/EntitiesDescriptor/testshib-providers.xml');
        $all = $store->all();
        $this->assertCount(2, $all);
        $this->assertEquals('https://idp.testshib.org/idp/shibboleth', $all[0]->getEntityID());
        $this->assertEquals('https://sp.testshib.org/shibboleth-sp', $all[1]->getEntityID());
    }

    public function test_get_returns_null_when_file_entity_id_does_not_match()
    {
        $store = new FileEntityDescriptorStore(__DIR__.'/../../../../../../resources/sample/EntityDescriptor/idp-ed.xml');
        $ed = $store->get('http://foo.com');
        $this->assertNull($ed);
    }
}
