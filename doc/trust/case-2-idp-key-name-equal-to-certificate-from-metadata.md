Case 2 - IDP KeyName equal to certificate from own metadata
===========================================================

This case is hypothetical so for and is not proven in practice with ADFS and TestShip default configuration.
But since no argument that will prevent it is found in the SAML specs so far, it seems reasonable and possible, so
an effort will be put for it's implementation.

Overview
--------

IDP has **IDP-pk1** and matching **IDP-cert-1-1** with DN="CN: IDP" used for both signing and encryption.
SP has **SP-pk1** and matching **SP-cert-1-1** with DN="CN: SP" used for both signing and encryption.

IDP exposes **IDP-cert-1-1** to public in its metadata.
SP exposes **SP-cert-1-1** to public in its metadata.

IDP puts distinguished name (DN) in messages as KeyInfo KeyName both in Signature and EncryptedData.

SP receives SAML signed Response message with encrypted & signed assertions by HTTP POST binding according to
Web Browser SSO profile.


Verifying Response Signature
----------------------------

SP finds **CN: IDP** in Response Signature KeyInfo KeyName.

SP checks if it has certificate with such DN registered in trusted certificates for IDP entity ID, and it finds **IDP-cert-1-1**.

SP verifies the signature with the certificate **IDP-cert-1-1** from own registry since it's matches to specified Signature KeyInfo KeyName.


Decrypting Assertion
--------------------

SP finds **CN: SP** in EncryptedAssertion EncryptedData KeyInfo KeyName.

SP checks if it has certificate with such DN registered in trusted certificates for IDP entity ID, and it finds it's own key pair.

SP decrypts symmetric key chipper with **SP-pk1** - the private key of it's own key pair - and gets the symmetric key.

SP decrypts assertion chipped with symmetric key and gets the assertion.


Verifying Assertion Signature
-----------------------------

Similarily to verifying Response Signature...

SP finds **CN: IDP** in Assertion Signature KeyInfo KeyName.

SP finds if it has a registered trusted certificate with such DN, and finds **IDP-cert-1-1**.

SP verifies Assertion Signature with **IDP-cert-1-1**.
