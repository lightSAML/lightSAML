<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests\Binding;

use LightSaml\Binding\HttpRedirectBinding;
use LightSaml\Context\Profile\MessageContext;
use LightSaml\Tests\BaseTestCase;
use Symfony\Component\HttpFoundation\Request;

class HttpRedirectBindingTest extends BaseTestCase
{
    public function test__receive_throws_when_no_message()
    {
        $this->expectException(\LightSaml\Error\LightSamlBindingException::class);
        $this->expectExceptionMessage('Missing SAMLRequest or SAMLResponse parameter');

        $request = new Request();

        $binding = new HttpRedirectBinding();

        $messageContext = new MessageContext();

        $binding->receive($request, $messageContext);
    }
}
