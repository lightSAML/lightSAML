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
    public const COMMON_NAME = 'http://schemas.xmlsoap.org/claims/CommonName';
    public const EMAIL_ADDRESS = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress';
    public const GIVEN_NAME = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname';
    public const NAME = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name';
    public const UPN = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/upn';
    public const ADFS_1_EMAIL = 'http://schemas.xmlsoap.org/claims/EmailAddress';
    public const GROUP = 'http://schemas.xmlsoap.org/claims/Group';
    public const ADFS_1_UPN = 'http://schemas.xmlsoap.org/claims/UPN';
    public const ROLE = 'http://schemas.microsoft.com/ws/2008/06/identity/claims/role';
    public const SURNAME = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname';
    public const PPID = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/privatepersonalidentifier';
    public const NAME_ID = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/nameidentifier';
    public const AUTHENTICATION_TIMESTAMP = 'http://schemas.microsoft.com/ws/2008/06/identity/claims/authenticationinstant';
    public const AUTHENTICATION_METHOD = 'http://schemas.microsoft.com/ws/2008/06/identity/claims/authenticationmethod';
    public const WINDOWS_ACCOUNT_NAME = 'http://schemas.microsoft.com/ws/2008/06/identity/claims/windowsaccountname';
}
