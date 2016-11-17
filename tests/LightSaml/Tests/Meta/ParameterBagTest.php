<?php

namespace LightSaml\Tests\Meta;

use LightSaml\Meta\ParameterBag;

class ParameterBagTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_wout_arguments()
    {
        new ParameterBag();
    }

    public function test_all()
    {
        $bag = new ParameterBag(array('foo' => 'bar'));
        $this->assertEquals(array('foo' => 'bar'), $bag->all(), '->all() gets all the input');
    }

    public function test_keys()
    {
        $bag = new ParameterBag(array('foo' => 'bar'));
        $this->assertEquals(array('foo'), $bag->keys());
    }

    public function test_add()
    {
        $bag = new ParameterBag(array('foo' => 'bar'));
        $bag->add(array('bar' => 'bas'));
        $this->assertEquals(array('foo' => 'bar', 'bar' => 'bas'), $bag->all());
    }

    public function test_remove()
    {
        $bag = new ParameterBag(array('foo' => 'bar'));
        $bag->add(array('bar' => 'bas'));
        $this->assertEquals(array('foo' => 'bar', 'bar' => 'bas'), $bag->all());
        $bag->remove('bar');
        $this->assertEquals(array('foo' => 'bar'), $bag->all());
    }

    public function test_replace()
    {
        $bag = new ParameterBag(array('foo' => 'bar'));

        $bag->replace(array('FOO' => 'BAR'));
        $this->assertEquals(array('FOO' => 'BAR'), $bag->all(), '->replace() replaces the input with the argument');
        $this->assertFalse($bag->has('foo'), '->replace() overrides previously set the input');
    }

    public function test_get()
    {
        $bag = new ParameterBag(array('foo' => 'bar', 'null' => null));

        $this->assertEquals('bar', $bag->get('foo'), '->get() gets the value of a parameter');
        $this->assertEquals('default', $bag->get('unknown', 'default'), '->get() returns second argument as default if a parameter is not defined');
        $this->assertNull($bag->get('null', 'default'), '->get() returns null if null is set');
    }

    public function test_get_does_not_use_deep_by_default()
    {
        $bag = new ParameterBag(array('foo' => array('bar' => 'moo')));

        $this->assertNull($bag->get('foo[bar]'));
    }

    public function test_set()
    {
        $bag = new ParameterBag(array());

        $bag->set('foo', 'bar');
        $this->assertEquals('bar', $bag->get('foo'), '->set() sets the value of parameter');

        $bag->set('foo', 'baz');
        $this->assertEquals('baz', $bag->get('foo'), '->set() overrides previously set parameter');
    }

    public function test_has()
    {
        $bag = new ParameterBag(array('foo' => 'bar'));

        $this->assertTrue($bag->has('foo'), '->has() returns true if a parameter is defined');
        $this->assertFalse($bag->has('unknown'), '->has() return false if a parameter is not defined');
    }

    public function test_get_iterator()
    {
        $parameters = array('foo' => 'bar', 'hello' => 'world');
        $bag = new ParameterBag($parameters);

        $i = 0;
        foreach ($bag as $key => $val) {
            ++$i;
            $this->assertEquals($parameters[$key], $val);
        }

        $this->assertEquals(count($parameters), $i);
    }

    public function test_count()
    {
        $parameters = array('foo' => 'bar', 'hello' => 'world');
        $bag = new ParameterBag($parameters);

        $this->assertEquals(count($parameters), count($bag));
    }

    public function test_serialization()
    {
        $expectedData = ['a' => 'aaa', 'b' => 2, 'c' => [1, 2, 3]];
        $bag = new ParameterBag($expectedData);
        $serialized = serialize($bag);
        /** @var ParameterBag $other */
        $other = unserialize($serialized);
        $this->assertInstanceOf(ParameterBag::class, $other);
        $this->assertEquals($expectedData, $other->all());
    }
}
