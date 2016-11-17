<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\State\Request;

final class RequestStateParameters
{
    const ID = 'id';
    const TYPE = 'type';
    const TIMESTAMP = 'ts';
    const PARTY = 'party';
    const RELAY_STATE = 'relay_state';
    const NAME_ID = 'name_id';
    const NAME_ID_FORMAT = 'name_id_format';
    const SESSION_INDEX = 'session_index';

    private function __construct()
    {
    }
}
