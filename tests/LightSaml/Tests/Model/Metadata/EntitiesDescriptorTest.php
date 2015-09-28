<?php

namespace LightSaml\Tests\Model\Metadata;

use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\Model\Metadata\EntitiesDescriptor;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\SamlConstants;

class EntitiesDescriptorTest extends \PHPUnit_Framework_TestCase
{
    public function testImplementSamlElementInterface()
    {
        $rc = new \ReflectionClass('LightSaml\Model\Metadata\EntitiesDescriptor');
        $rc->implementsInterface('LightSaml\Model\SamlElementInterface');
    }

    public function testSetValidStringToValidUntil()
    {
        $ed = new EntitiesDescriptor();
        $ed->setValidUntil('2013-10-27T11:55:37.035Z');
    }

    public function testSetPositiveIntToValidUntil()
    {
        $ed = new EntitiesDescriptor();
        $ed->setValidUntil(123456);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowOnSetInvalidStringToValidUntil()
    {
        $ed = new EntitiesDescriptor();
        $ed->setValidUntil('something');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowOnSetNegativeIntToValidUntil()
    {
        $ed = new EntitiesDescriptor();
        $ed->setValidUntil(-1);
    }

    public function testSetValidPeriodStringToCacheDuration()
    {
        $ed = new EntitiesDescriptor();
        $ed->setCacheDuration('P3D');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowOnInvalidPeriodStringSetToCacheDuration()
    {
        $ed = new EntitiesDescriptor();
        $ed->setCacheDuration('83D2Y');
    }

    public function testAddItemEntitiesDescriptor()
    {
        $ed = new EntitiesDescriptor();
        $ed->addItem(new EntitiesDescriptor());
    }

    public function testAddItemEntityDescriptor()
    {
        $ed = new EntitiesDescriptor();
        $ed->addItem(new EntityDescriptor());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowOnInvalidObjectTypeGivenToAddItem()
    {
        $ed = new EntitiesDescriptor();
        $ed->addItem(new \stdClass());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowOnArrayGivenToAddItem()
    {
        $ed = new EntitiesDescriptor();
        $ed->addItem(array());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowOnStringGivenToAddItem()
    {
        $ed = new EntitiesDescriptor();
        $ed->addItem('foo');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowOnIntGivenToAddItem()
    {
        $ed = new EntitiesDescriptor();
        $ed->addItem(123);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowWhenItselfGivenToAddItem()
    {
        $ed = new EntitiesDescriptor();
        $ed->addItem($ed);
    }

    public function testContainsItemWork()
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

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowWhenCircularReferenceDetectedInAddItem()
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

        $esd3->addItem($esd1);
    }

    public function testReturnRecursivelyAllEntityDescriptors()
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

    public function testSerializer()
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

    public function testDeserialize()
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
        $esd->deserialize($context->getDocument()->firstChild, $context);

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
