<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests\Model\Protocol;

use LightSaml\Model\Protocol\Status;
use LightSaml\Model\Protocol\StatusCode;
use LightSaml\SamlConstants;
use LightSaml\Tests\BaseTestCase;

class StatusTest extends BaseTestCase
{
    public function test_status_set_message_constructor()
    {
        $message = 'Test message';
        $status = new Status(new StatusCode(SamlConstants::STATUS_SUCCESS), $message);
        $this->assertEquals($status->getStatusMessage(), $message);
    }

    public function test_status_set_message_setter()
    {
        $message = 'Test message';
        $status = new Status(new StatusCode(SamlConstants::STATUS_SUCCESS));
        $status->setStatusMessage($message);
        $this->assertEquals($status->getStatusMessage(), $message);
    }
}
