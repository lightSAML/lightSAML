<?php

namespace LightSaml\Profile;

abstract class Profiles
{
    const METADATA = 'metadata';

    const SSO_IDP_RECEIVE_AUTHN_REQUEST = 'sso_idp_receive_authn_req';
    const SSO_IDP_SEND_RESPONSE = 'sso_idp_send_response';
    const SSO_SP_SEND_AUTHN_REQUEST = 'sso_sp_send_authn_req';
    const SSO_SP_RECEIVE_RESPONSE = 'sso_sp_receive_response';
}
