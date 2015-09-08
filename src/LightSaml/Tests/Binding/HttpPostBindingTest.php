<?php

namespace LightSaml\Tests\Binding;

use LightSaml\Binding\HttpPostBinding;
use LightSaml\Context\Profile\MessageContext;
use Symfony\Component\HttpFoundation\Request;

class HttpPostBindingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \LightSaml\Error\LightSamlBindingException
     * @expectedExceptionMessage Missing SAMLRequest or SAMLResponse parameter
     */
    public function testReceiveThrowsWhenNoMessage()
    {
        $request = new Request();

        $binding = new HttpPostBinding();

        $messageContext = new MessageContext();

        $binding->receive($request, $messageContext);
    }
}
