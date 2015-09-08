Case 1 - IDP X509Data certificate from own metadata
===================================================

This case is proven and reproducible both with default ADFS and TestShip default configuration.

Overview
--------

IDP has **IDP-pk1** and matching **IDP-cert-1-1** used for both signing and encryption.
SP has **SP-pk1** and matching **SP-cert-1-1** used for both signing and encryption.

IDP exposes **IDP-cert-1-1** to public in its metadata.
SP exposes **SP-cert-1-1** to public in its metadata.

IDP puts KeyInfo X509Data with a certificate both in Signature and EncryptedData

SP receives SAML signed Response message with encrypted & signed assertions by HTTP POST binding according to
Web Browser SSO profile.


Verifying Response Signature
----------------------------

SP finds **IDP-cert-1-1** in Response Signature KeyInfo X509Data.

SP verifies certificate authority chain of **IDP-cert-1-1** against all trusted certificates its has for IDP entity ID.
SP knows for it's own key pair, and IDP metadata certificate **IDP-cert-1-1**.

Certificate authority verification does not succeed with SP key pair, but succeeds with **IDP-cert-1-1** since it's
the same key.

SP verifies the signature with the certificate **IDP-cert-1-1** from the Response Signature KeyInfo X509Data since it's
been verified it's a known trusted certificate.


Decrypting Assertion
--------------------

SP finds **SP-cert-1-1** in EncryptedAssertion EncryptedData KeyInfo X509Data.

SP verifies certificate authority chain of **SP-cert-1-1** against all trusted certificates its has for IDP entity ID.
SP knows for it's own key pair, and IDP certificate **IDP-cert-1-1**.

Certificate authority verification does not succeed with **IDP-cert-1-1**, but succeeds with SP key pair.

SP decrypts symmetric key cipher with **SP-pk1** - the private key of it's own key pair - and gets the symmetric key.

SP decrypts assertion cipher with symmetric key and gets the assertion XML.


Verifying Assertion Signature
-----------------------------

Similarily to [Verifying Response Signature](#Verifying+Response+Signature)...

SP finds **IDP-cert-1-1** in Assertion Signature KeyInfo X509Data.

SP verifies certificate authority chain of **IDP-cert-1-1** and finds it's trusted.

SP verifies Assertion Signature with **IDP-cert-1-1**.
