<?php

namespace LightSaml\Tests\Error;

use LightSaml\Error\LightSamlContextException;
use LightSaml\Tests\TestHelper;

class LightSamlContextExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function test_returns_context_its_constructed_with()
    {
        $context = TestHelper::getProfileContext();

        $exception = new LightSamlContextException($context, 'message');

        $this->assertSame($context, $exception->getContext());
    }
}
