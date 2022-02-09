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
    public const ID = 'id';
    public const TYPE = 'type';
    public const TIMESTAMP = 'ts';
    public const PARTY = 'party';
    public const RELAY_STATE = 'relay_state';
    public const NAME_ID = 'name_id';
    public const NAME_ID_FORMAT = 'name_id_format';
    public const SESSION_INDEX = 'session_index';

    private function __construct()
    {
    }
}
