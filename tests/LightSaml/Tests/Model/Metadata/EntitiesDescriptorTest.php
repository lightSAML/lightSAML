<?php

namespace LightSaml\Tests\Model\Metadata;

use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\Model\Metadata\EntitiesDescriptor;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\SamlConstants;
use LightSaml\Tests\BaseTestCase;

class EntitiesDescriptorTest extends BaseTestCase
{
    public function test_implement_saml_element_interface()
    {
        $rc = new \ReflectionClass(\LightSaml\Model\Metadata\EntitiesDescriptor::class);
        $this->assertTrue($rc->implementsInterface(\LightSaml\Model\SamlElementInterface::class));
    }

    public function test_set_valid_string_to_valid_until()
    {
        $ed = new EntitiesDescriptor();
        $ed->setValidUntil('2013-10-27T11:55:37.035Z');
        $this->assertTrue(true);
    }

    public function test_set_positive_int_to_valid_until()
    {
        $ed = new EntitiesDescriptor();
        $ed->setValidUntil(123456);
        $this->assertTrue(true);
    }

    public function test_throw_on_set_invalid_string_to_valid_until()
    {
        $this->expectException(\InvalidArgumentException::class);
        $ed = new EntitiesDescriptor();
        $ed->setValidUntil('something');
    }

    public function test_throw_on_set_negative_int_to_valid_until()
    {
        $this->expectException(\InvalidArgumentException::class);
        $ed = new EntitiesDescriptor();
        $ed->setValidUntil(-1);
    }

    public function test_set_valid_period_string_to_cache_duration()
    {
        $ed = new EntitiesDescriptor();
        $ed->setCacheDuration('P3D');
        $this->assertTrue(true);
    }

    public function test_throw_on_invalid_period_string_set_to_cache_duration()
    {
        $this->expectException(\InvalidArgumentException::class);
        $ed = new EntitiesDescriptor();
        $ed->setCacheDuration('83D2Y');
    }

    public function test_add_item_entities_descriptor()
    {
        $ed = new EntitiesDescriptor();
        $ed->addItem(new EntitiesDescriptor());
        $this->assertTrue(true);
    }

    public function test_add_item_entity_descriptor()
    {
        $ed = new EntitiesDescriptor();
        $ed->addItem(new EntityDescriptor());
        $this->assertTrue(true);
    }

    public function test_throw_on_invalid_object_type_given_to_add_item()
    {
        $this->expectException(\InvalidArgumentException::class);
        $ed = new EntitiesDescriptor();
        $ed->addItem(new \stdClass());
    }

    public function test_throw_on_array_given_to_add_item()
    {
        $this->expectException(\InvalidArgumentException::class);
        $ed = new EntitiesDescriptor();
        $ed->addItem(array());
    }

    public function test_throw_on_string_given_to_add_item()
    {
        $this->expectException(\InvalidArgumentException::class);
        $ed = new EntitiesDescriptor();
        $ed->addItem('foo');
    }

    public function test_throw_on_int_given_to_add_item()
    {
        $this->expectException(\InvalidArgumentException::class);
        $ed = new EntitiesDescriptor();
        $ed->addItem(123);
    }

    public function test_throw_when_itself_given_to_add_item()
    {
        $this->expectException(\InvalidArgumentException::class);
        $ed = new EntitiesDescriptor();
        $ed->addItem($ed);
    }

    public function test_contains_item_work()
    {
        $o1 = new EntitiesDescriptor();
        $o2 = new EntityDescriptor('ed1');
        $o3 = new EntitiesDescriptor();
        $o4 = new EntityDescriptor('ed2');

        $x1 = new EntitiesDescriptor();
        $x2 = new EntityDescriptor('ed3');

        $o1->addItem($o2);
        $o1->addItem($o3);
        $o3->addItem($o4);

        $this->assertTrue($o1->containsItem($o2));
        $this->assertTrue($o1->containsItem($o3));
        $this->assertTrue($o1->containsItem($o4));
        $this->assertFalse($o1->containsItem($x1));
        $this->assertFalse($o1->containsItem($x2));

        $this->assertTrue($o3->containsItem($o4));
        $this->assertFalse($o3->containsItem($o1));
        $this->assertFalse($o3->containsItem($o2));
        $this->assertFalse($o3->containsItem($x1));
        $this->assertFalse($o3->containsItem($x2));
    }

    public function test_throw_when_circular_reference_detected_in_add_item()
    {
        $this->expectException(\InvalidArgumentException::class);
        $esd1 = new EntitiesDescriptor();
        $esd1->addItem(new EntityDescriptor('ed1'));
        $esd1->addItem(new EntityDescriptor('ed2'));

        $esd2 = new EntitiesDescriptor();
        $esd2->addItem(new EntityDescriptor('ed3'));
        $esd1->addItem($esd2);

        $esd3 = new EntitiesDescriptor();
        $esd3->addItem(new EntityDescriptor('ed4'));
        $esd2->addItem($esd3);

        $esd3->addItem($esd1);
    }

    public function test_return_recursively_all_entity_descriptors()
    {
        $esd1 = new EntitiesDescriptor();
        $esd1->addItem(new EntityDescriptor('ed1'));
        $esd1->addItem(new EntityDescriptor('ed2'));

        $esd2 = new EntitiesDescriptor();
        $esd2->addItem(new EntityDescriptor('ed3'));
        $esd1->addItem($esd2);

        $esd3 = new EntitiesDescriptor();
        $esd3->addItem(new EntityDescriptor('ed4'));
        $esd2->addItem($esd3);

        $all = $esd1->getAllEntityDescriptors();
        $this->assertCount(4, $all);
        $this->assertContainsOnlyInstancesOf('LightSaml\Model\Metadata\EntityDescriptor', $all);

        $this->assertEquals('ed1', $all[0]->getEntityID());
        $this->assertEquals('ed2', $all[1]->getEntityID());
        $this->assertEquals('ed3', $all[2]->getEntityID());
        $this->assertEquals('ed4', $all[3]->getEntityID());
    }

    public function test_serializer()
    {
        $esd = new EntitiesDescriptor();
        $esd->addItem(new EntityDescriptor('ed1'));
        $esd->addItem(new EntityDescriptor('ed2'));

        $esd2 = new EntitiesDescriptor();
        $esd2->addItem(new EntityDescriptor('ed3'));
        $esd->addItem($esd2);

        $ctx = new SerializationContext();
        $esd->serialize($ctx->getDocument(), $ctx);

        $xpath = new \DOMXPath($ctx->getDocument());
        $xpath->registerNamespace('md', SamlConstants::NS_METADATA);

        $this->assertEquals(1, $xpath->query('/md:EntitiesDescriptor')->length);
        $this->assertEquals(2, $xpath->query('/md:EntitiesDescriptor/md:EntityDescriptor')->length);
        $this->assertEquals(1, $xpath->query('/md:EntitiesDescriptor/md:EntityDescriptor[@entityID="ed1"]')->length);
        $this->assertEquals(1, $xpath->query('/md:EntitiesDescriptor/md:EntityDescriptor[@entityID="ed2"]')->length);
        $this->assertEquals(1, $xpath->query('/md:EntitiesDescriptor/md:EntitiesDescriptor')->length);
        $this->assertEquals(1, $xpath->query('/md:EntitiesDescriptor/md:EntitiesDescriptor/md:EntityDescriptor[@entityID="ed3"]')->length);
    }

    public function test_deserialize()
    {
        $xml = <<<EOT
<?xml version="1.0"?>
<md:EntitiesDescriptor ID="esd1" Name="first" validUntil="2013-10-27T11:55:37.035Z" cacheDuration="P1D" xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata">
<md:EntityDescriptor entityID="ed1"/>
<md:EntityDescriptor entityID="ed2"/>
<md:EntitiesDescriptor ID="esd2" Name="second">
    <md:EntityDescriptor entityID="ed3"/>
</md:EntitiesDescriptor>
</md:EntitiesDescriptor>
EOT;

        $context = new DeserializationContext();
        $context->getDocument()->loadXML($xml);

        $esd = new EntitiesDescriptor();
        $esd->deserialize($context->getDocument(), $context);

        $this->assertEquals('esd1', $esd->getId());
        $this->assertEquals('first', $esd->getName());
        $this->assertEquals(1382874937, $esd->getValidUntilTimestamp());
        $this->assertEquals('P1D', $esd->getCacheDuration());

        $items = $esd->getAllItems();
        $this->assertCount(3, $items);

        $this->assertInstanceOf('LightSaml\Model\Metadata\EntityDescriptor', $items[0]);
        $this->assertInstanceOf('LightSaml\Model\Metadata\EntityDescriptor', $items[1]);
        $this->assertInstanceOf('LightSaml\Model\Metadata\EntitiesDescriptor', $items[2]);
    }
}
