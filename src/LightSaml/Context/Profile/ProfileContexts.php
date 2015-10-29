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
    const INBOUND_MESSAGE = 'inbound_message';
    const OUTBOUND_MESSAGE = 'outbound_message';
    const OWN_ENTITY = 'own_entity';
    const PARTY_ENTITY = 'party_entity';
    const DESERIALIZATION = 'deserialization';
    const SERIALIZATION = 'serialization';
    const HTTP_REQUEST = 'http_request';
    const HTTP_RESPONSE = 'http_response';
    const ENDPOINT = 'endpoint';
    const REQUEST_STATE = 'request_state';
    const LOGOUT = 'logout';
    const EXCEPTION = 'exception';
}
