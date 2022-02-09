<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Context\Profile;

class ProfileContexts
{
    public const INBOUND_MESSAGE = 'inbound_message';
    public const OUTBOUND_MESSAGE = 'outbound_message';
    public const OWN_ENTITY = 'own_entity';
    public const PARTY_ENTITY = 'party_entity';
    public const DESERIALIZATION = 'deserialization';
    public const SERIALIZATION = 'serialization';
    public const HTTP_REQUEST = 'http_request';
    public const HTTP_RESPONSE = 'http_response';
    public const ENDPOINT = 'endpoint';
    public const REQUEST_STATE = 'request_state';
    public const LOGOUT = 'logout';
    public const EXCEPTION = 'exception';
}
