<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Event;

abstract class Events
{
    const BINDING_MESSAGE_RECEIVED = 'lightsaml.binding_message_received';
    const BINDING_MESSAGE_SENT = 'lightsaml.binding_message_sent';
    const BEFORE_ENCRYPT = 'lightsaml.before_encrypt';
}
