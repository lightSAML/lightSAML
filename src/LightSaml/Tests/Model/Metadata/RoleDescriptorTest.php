<?php

namespace LightSaml\Tests\Model\Metadata;

use LightSaml\Model\XmlDSig\SignatureWriter;
use LightSaml\Tests\Fixtures\Model\Metadata\RoleDescriptorMock;

class RoleDescriptorTest extends \PHPUnit_Framework_TestCase
{
    public function testSetValidCacheDuration()
    {
        $rd = new RoleDescriptorMock();
        $rd->setCacheDuration($expectedValue = 'P1Y');
        $this->assertEquals($expectedValue, $rd->getCacheDuration());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetInvalidCacheDuration()
    {
        $rd = new RoleDescriptorMock();
        $rd->setCacheDuration('123');
    }

    public function testSetErrorUrl()
    {
        $rd = new RoleDescriptorMock();
        $rd->setErrorURL($expectedValue = 'http://example.com/error');
        $this->assertEquals($expectedValue, $rd->getErrorURL());
    }

    public function testSetId()
    {
        $rd = new RoleDescriptorMock();
        $rd->setID($expectedValue = 'id-123');
        $this->assertEquals($expectedValue, $rd->getID());
    }

    public function testGetFirstKeyDescriptorReturnsNullWhenEmpty()
    {
        $rd = new RoleDescriptorMock();
        $this->assertNull($rd->getFirstKeyDescriptor());
    }

    public function testAddSignature()
    {
        $rd = new RoleDescriptorMock();
        $rd->addSignature($s1 = new SignatureWriter());
        $rd->addSignature($s2 = new SignatureWriter());
        $this->assertCount(2, $arr = $rd->getAllSignatures());
        $this->assertSame($s1, $arr[0]);
        $this->assertSame($s2, $arr[1]);
    }

    public function testSetValidUntilString()
    {
        $rd = new RoleDescriptorMock();
        $rd->setValidUntil('2013-10-27T11:55:37Z');
        $this->assertEquals(1382874937, $rd->getValidUntilTimestamp());
    }

    public function testSetValidUntilTimestamp()
    {
        $rd = new RoleDescriptorMock();
        $rd->setValidUntil($expectedValue = 1382874937);
        $this->assertEquals(1382874937, $rd->getValidUntilTimestamp());
    }

    public function testSetValidUntilDateTime()
    {
        $rd = new RoleDescriptorMock();
        $rd->setValidUntil(new \DateTime('2013-10-27T11:55:37Z'));
        $this->assertEquals(1382874937, $rd->getValidUntilTimestamp());
    }

    public function testGetValidUntilString()
    {
        $rd = new RoleDescriptorMock();
        $rd->setValidUntil($expectedValue = '2013-10-27T11:55:37Z');
        $this->assertEquals('2013-10-27T11:55:37Z', $rd->getValidUntilString());
    }
}
