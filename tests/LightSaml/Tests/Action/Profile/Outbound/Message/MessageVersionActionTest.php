<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests\Action\Profile\Outbound\Message;

use LightSaml\Action\Profile\Outbound\Message\MessageVersionAction;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\SamlConstants;
use LightSaml\Tests\BaseTestCase;

class MessageVersionActionTest extends BaseTestCase
{
    public function test_constructs_with_logger_and_version()
    {
        new MessageVersionAction($this->getLoggerMock(), SamlConstants::VERSION_20);
        $this->assertTrue(true);
    }

    public function test_sets_outbounding_message_version_to_value_from_constructor()
    {
        $action = new MessageVersionAction($this->getLoggerMock(), $value = SamlConstants::VERSION_20);

        $context = $this->getProfileContext();
        $context->getOutboundContext()->setMessage($message = new AuthnRequest());

        $action->execute($context);

        $this->assertEquals($value, $message->getVersion());
    }
}
