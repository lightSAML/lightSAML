<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Profile;

abstract class Profiles
{
    public const METADATA = 'metadata';

    public const SSO_IDP_RECEIVE_AUTHN_REQUEST = 'sso_idp_receive_authn_req';
    public const SSO_IDP_SEND_RESPONSE = 'sso_idp_send_response';
    public const SSO_SP_SEND_AUTHN_REQUEST = 'sso_sp_send_authn_req';
    public const SSO_SP_RECEIVE_RESPONSE = 'sso_sp_receive_response';
}
