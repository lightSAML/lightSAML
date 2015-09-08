Case 3 - IDP X509Data Certificate issued by the certificate from metadata
=========================================================================

This case is hypothetical so for and is not proven in practice with default ADFS and TestShip default configuration.
But since no argument that will prevent it is found in the SAML specs so far, it seems reasonable and possible, so
an effort will be put for it's implementation.

The algorithm itself for this case seems to be the same as for the [Case-1](case-1-idp-x509data-certificate-from-own-metadata.md),
but regardless the case is documented to point out to the possibility and eventually implemented functionality.

Overview
--------

IDP has **IDP-pk1** and matching **IDP-cert-1-1** as root CA.
IDP has **IDP-pk2** and matching **IDP-cert-2-1** used for both signing and encryption.
SP has **SP-pk1** and matching **SP-cert-1-1** used for both signing and encryption.

IDP exposes **IDP-cert-1-1** to public in its metadata.
SP exposes **SP-cert-1-1** to public in its metadata.

IDP puts **IDP-cert-2-1** KeyInfo X509Data (both Signature and EncryptedData)

SP receives SAML signed Response message with encrypted & signed assertions by HTTP POST binding according to
Web Browser SSO profile.


Verifying Response Signature
----------------------------

SP finds **IDP-cert-2-1** in Response Signature KeyInfo X509Data.

SP verifies certificate authority chain of **IDP-cert-2-1** against all trusted certificates its has for IDP entity ID.
SP knows for it's own key pair, and IDP metadata certificate **IDP-cert-1-1**.

Certificate authority verification does not succeed with SP key pair, but succeeds with **IDP-cert-1-1** since that the
issuer of **IDP-cert-2-1**.

SP verifies the signature with the certificate **IDP-cert-2-1** from the Response Signature KeyInfo X509Data since it's
been verified it's a known trusted certificate.


Decrypting Assertion
--------------------

Similarily to [Case-1 Decrypting Assertion](case-2-idp-key-name-equal-to-certificate-from-metadata.md#Decrypting+Assertion)...

SP finds **SP-cert-1-1** in EncryptedAssertion EncryptedData KeyInfo X509Data.

SP verifies certificate authority chain of **SP-cert-1-1** and it succeeds with own SP key pair.

SP decrypts symmetric key chipper with **SP-pk1** - the private key of it's own key pair - and gets the symmetric key.

SP decrypts assertion chipped with symmetric key and gets the assertion.


Verifying Assertion Signature
-----------------------------

Similarily to [Verifying Response Signature](#Verifying+Response+Signature)...

SP finds **IDP-cert-2-1** in Assertion Signature KeyInfo X509Data.

SP verifies certificate authority chain of **IDP-cert-1-1** and finds it's trusted.

SP verifies Assertion Signature with **IDP-cert-2-1**.
