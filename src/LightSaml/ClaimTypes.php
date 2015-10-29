<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml;

class ClaimTypes
{
    const COMMON_NAME = 'http://schemas.xmlsoap.org/claims/CommonName';
    const EMAIL_ADDRESS = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress';
    const GIVEN_NAME = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname';
    const NAME = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name';
    const UPN = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/upn';
    const ADFS_1_EMAIL = 'http://schemas.xmlsoap.org/claims/EmailAddress';
    const GROUP = 'http://schemas.xmlsoap.org/claims/Group';
    const ADFS_1_UPN = 'http://schemas.xmlsoap.org/claims/UPN';
    const ROLE = 'http://schemas.microsoft.com/ws/2008/06/identity/claims/role';
    const SURNAME = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname';
    const PPID = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/privatepersonalidentifier';
    const NAME_ID = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/nameidentifier';
    const AUTHENTICATION_TIMESTAMP = 'http://schemas.microsoft.com/ws/2008/06/identity/claims/authenticationinstant';
    const AUTHENTICATION_METHOD = 'http://schemas.microsoft.com/ws/2008/06/identity/claims/authenticationmethod';
    const WINDOWS_ACCOUNT_NAME = 'http://schemas.microsoft.com/ws/2008/06/identity/claims/windowsaccountname';
}
