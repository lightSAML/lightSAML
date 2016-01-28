<?php

namespace LightSaml\Tests\Functional\Model\Metadata;

use LightSaml\Model\Metadata\EntitiesDescriptor;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Model\Metadata\Metadata;

class MetadataTest extends \PHPUnit_Framework_TestCase
{
    public function test_loads_from_entity_descriptor()
    {
        $ed = Metadata::fromFile(__DIR__.'/../../../../../../resources/sample/EntityDescriptor/idp2-ed.xml');
        $this->assertInstanceOf(EntityDescriptor::class, $ed);
        $this->assertEquals('https://B1.bead.loc/adfs/services/trust', $ed->getEntityID());
    }

    public function test_loads_from_entities_descriptor()
    {
        $eds = Metadata::fromFile(__DIR__.'/../../../../../../resources/sample/EntitiesDescriptor/testshib-providers.xml');
        $this->assertInstanceOf(EntitiesDescriptor::class, $eds);
        $this->assertCount(2, $eds->getAllEntityDescriptors());
    }
}
