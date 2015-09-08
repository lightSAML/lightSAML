<?php

namespace LightSaml\Event;

abstract class Events
{
    const BINDING_MESSAGE_RECEIVED = 'lightsaml.binding_message_received';
    const BINDING_MESSAGE_SENT = 'lightsaml.binding_message_sent';
    const BEFORE_ENCRYPT = 'lightsaml.before_encrypt';
}
