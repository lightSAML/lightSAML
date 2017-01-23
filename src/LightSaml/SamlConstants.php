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

abstract class SamlConstants
{
    const PROTOCOL_SAML2 = 'urn:oasis:names:tc:SAML:2.0:protocol';
    const PROTOCOL_SAML1 = 'urn:oasis:names:tc:SAML:1.0:protocol';
    const PROTOCOL_SAML11 = 'urn:oasis:names:tc:SAML:1.1:protocol';
    const PROTOCOL_SHIB1 = 'urn:mace:shibboleth:1.0';
    const PROTOCOL_WS_FED = 'http://schemas.xmlsoap.org/ws/2003/07/secext???';

    const VERSION_20 = '2.0';

    const NS_PROTOCOL = 'urn:oasis:names:tc:SAML:2.0:protocol';
    const NS_METADATA = 'urn:oasis:names:tc:SAML:2.0:metadata';
    const NS_ASSERTION = 'urn:oasis:names:tc:SAML:2.0:assertion';
    const NS_XMLDSIG = 'http://www.w3.org/2000/09/xmldsig#';

    const NAME_ID_FORMAT_NONE = null;
    const NAME_ID_FORMAT_ENTITY = 'urn:oasis:names:tc:SAML:2.0:nameid-format:entity';
    const NAME_ID_FORMAT_PERSISTENT = 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent';
    const NAME_ID_FORMAT_TRANSIENT = 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient';
    const NAME_ID_FORMAT_EMAIL = 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress';
    const NAME_ID_FORMAT_SHIB_NAME_ID = 'urn:mace:shibboleth:1.0:nameIdentifier';
    const NAME_ID_FORMAT_X509_SUBJECT_NAME = 'urn:oasis:names:tc:SAML:1.1:nameid-format:X509SubjectName';
    const NAME_ID_FORMAT_WINDOWS = 'urn:oasis:names:tc:SAML:1.1:nameid-format:WindowsDomainQualifiedName';
    const NAME_ID_FORMAT_KERBEROS = 'urn:oasis:names:tc:SAML:2.0:nameid-format:kerberos';
    const NAME_ID_FORMAT_UNSPECIFIED = 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified';

    const BINDING_SAML2_HTTP_REDIRECT = 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect';
    const BINDING_SAML2_HTTP_POST = 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST';
    const BINDING_SAML2_HTTP_ARTIFACT = 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Artifact';
    const BINDING_SAML2_SOAP = 'urn:oasis:names:tc:SAML:2.0:bindings:SOAP';
    const BINDING_SAML2_HTTP_POST_SIMPLE_SIGN = 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST-SimpleSign';
    const BINDING_SHIB1_AUTHN_REQUEST = 'urn:mace:shibboleth:1.0:profiles:AuthnRequest';
    const BINDING_SAML1_BROWSER_POST = 'urn:oasis:names:tc:SAML:1.0:profiles:browser-post';
    const BINDING_SAML1_ARTIFACT1 = 'urn:oasis:names:tc:SAML:1.0:profiles:artifact-01';
    const BINDING_WS_FED_WEB_SVC = 'http://schemas.xmlsoap.org/ws/2003/07/secext';

    const STATUS_SUCCESS = 'urn:oasis:names:tc:SAML:2.0:status:Success';
    const STATUS_REQUESTER = 'urn:oasis:names:tc:SAML:2.0:status:Requester';
    const STATUS_RESPONDER = 'urn:oasis:names:tc:SAML:2.0:status:Responder';
    const STATUS_VERSION_MISMATCH = 'urn:oasis:names:tc:SAML:2.0:status:VersionMismatch';
    const STATUS_NO_PASSIVE = 'urn:oasis:names:tc:SAML:2.0:status:NoPassive';
    const STATUS_PARTIAL_LOGOUT = 'urn:oasis:names:tc:SAML:2.0:status:PartialLogout';
    const STATUS_PROXY_COUNT_EXCEEDED = 'urn:oasis:names:tc:SAML:2.0:status:ProxyCountExceeded';
    const STATUS_INVALID_NAME_ID_POLICY = 'urn:oasis:names:tc:SAML:2.0:status:InvalidNameIDPolicy';
    const STATUS_UNSUPPORTED_BINDING = 'urn:oasis:names:tc:SAML:2.0:status:UnsupportedBinding';

    const XMLSEC_TRANSFORM_ALGORITHM_ENVELOPED_SIGNATURE = 'http://www.w3.org/2000/09/xmldsig#enveloped-signature';

    const CONSENT_UNSPECIFIED = 'urn:oasis:names:tc:SAML:2.0:consent:unspecified';

    const CONFIRMATION_METHOD_BEARER = 'urn:oasis:names:tc:SAML:2.0:cm:bearer';
    const CONFIRMATION_METHOD_HOK = 'urn:oasis:names:tc:SAML:2.0:cm:holder-of-key';
    const CONFIRMATION_METHOD_SENDER_VOUCHES = 'urn:oasis:names:tc:SAML:2.0:cm:sender-vouches';

    const AUTHN_CONTEXT_PASSWORD = 'urn:oasis:names:tc:SAML:2.0:ac:classes:Password';
    const AUTHN_CONTEXT_UNSPECIFIED = 'urn:oasis:names:tc:SAML:2.0:ac:classes:unspecified';
    const AUTHN_CONTEXT_PASSWORD_PROTECTED_TRANSPORT = 'urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport';
    const AUTHN_CONTEXT_WINDOWS = 'urn:federation:authentication:windows';

    const ENCODING_DEFLATE = 'urn:oasis:names:tc:SAML:2.0:bindings:URL-Encoding:DEFLATE';

    const LOGOUT_REASON_USER = 'urn:oasis:names:tc:SAML:2.0:logout:user';
    const LOGOUT_REASON_ADMIN = 'urn:oasis:names:tc:SAML:2.0:logout:admin';
    const LOGOUT_REASON_GLOBAL_TIMEOUT = 'urn:oasis:names:tc:SAML:2.0:logout:global-timeout';
    const LOGOUT_REASON_SP_TIMEOUT = 'urn:oasis:names:tc:SAML:2.0:logout:sp-timeout';

    const XMLDSIG_DIGEST_MD5 = 'http://www.w3.org/2001/04/xmldsig-more#md5';

    const ATTRIBUTE_NAME_FORMAT_UNSPECIFIED = 'urn:oasis:names:tc:SAML:2.0:attrname-format:unspecified';

    /**
     * @param string $value
     *
     * @return bool
     */
    public static function isProtocolValid($value)
    {
        static $arr = array(
            self::PROTOCOL_SAML2,
            self::PROTOCOL_SAML1,
            self::PROTOCOL_SAML11,
            self::PROTOCOL_SHIB1,
            self::PROTOCOL_WS_FED,
        );

        return in_array($value, $arr);
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public static function isNsValid($value)
    {
        static $arr = array(
            self::NS_PROTOCOL,
            self::NS_METADATA,
            self::NS_ASSERTION,
            self::NS_XMLDSIG,
        );

        return in_array($value, $arr);
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public static function isNameIdFormatValid($value)
    {
        static $arr = array(
            self::NAME_ID_FORMAT_NONE,
            self::NAME_ID_FORMAT_ENTITY,
            self::NAME_ID_FORMAT_PERSISTENT,
            self::NAME_ID_FORMAT_TRANSIENT,
            self::NAME_ID_FORMAT_EMAIL,
            self::NAME_ID_FORMAT_SHIB_NAME_ID,
            self::NAME_ID_FORMAT_X509_SUBJECT_NAME,
            self::NAME_ID_FORMAT_WINDOWS,
            self::NAME_ID_FORMAT_KERBEROS,
            self::NAME_ID_FORMAT_UNSPECIFIED,
        );

        return in_array($value, $arr);
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public static function isBindingValid($value)
    {
        static $arr = array(
            self::BINDING_SAML2_HTTP_REDIRECT,
            self::BINDING_SAML2_HTTP_POST,
            self::BINDING_SAML2_HTTP_ARTIFACT,
            self::BINDING_SAML2_SOAP,
            self::BINDING_SAML2_HTTP_POST_SIMPLE_SIGN,
            self::BINDING_SHIB1_AUTHN_REQUEST,
            self::BINDING_SAML1_BROWSER_POST,
            self::BINDING_SAML1_ARTIFACT1,
            self::BINDING_WS_FED_WEB_SVC,
        );

        return in_array($value, $arr);
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public static function isStatusValid($value)
    {
        static $arr = array(
            self::STATUS_SUCCESS,
            self::STATUS_REQUESTER,
            self::STATUS_RESPONDER,
            self::STATUS_VERSION_MISMATCH,
            self::STATUS_NO_PASSIVE,
            self::STATUS_PARTIAL_LOGOUT,
            self::STATUS_PROXY_COUNT_EXCEEDED,
            self::STATUS_INVALID_NAME_ID_POLICY,
            self::STATUS_UNSUPPORTED_BINDING,
        );

        return in_array($value, $arr);
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public static function isConfirmationMethodValid($value)
    {
        static $arr = array(
            self::CONFIRMATION_METHOD_BEARER,
            self::CONFIRMATION_METHOD_HOK,
            self::CONFIRMATION_METHOD_SENDER_VOUCHES,
        );

        return in_array($value, $arr);
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public static function isAuthnContextValid($value)
    {
        static $arr = array(
            self::AUTHN_CONTEXT_PASSWORD,
            self::AUTHN_CONTEXT_UNSPECIFIED,
            self::AUTHN_CONTEXT_PASSWORD_PROTECTED_TRANSPORT,
            self::AUTHN_CONTEXT_WINDOWS,
        );

        return in_array($value, $arr);
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public static function isLogoutReasonValid($value)
    {
        static $arr = array(
            self::LOGOUT_REASON_USER,
            self::LOGOUT_REASON_ADMIN,
            self::LOGOUT_REASON_GLOBAL_TIMEOUT,
            self::LOGOUT_REASON_SP_TIMEOUT,
        );

        return in_array($value, $arr);
    }
}
