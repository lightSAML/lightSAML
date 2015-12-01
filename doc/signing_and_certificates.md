SIGNING AND CERTIFICATES
========================

The LightSaml relies on the [xmlseclibs](https://github.com/robrichards/xmlseclibs) regarding signing and certificates
functionality. LightSaml has helper methods and was tested using xmlseclibs with PEM format only.


Creating self signed certificates
---------------------------------

Run the following command to generate pem certificate and key

``` bash
$ openssl req -new -x509 -days 3652 -nodes -out saml.crt -keyout saml.pem
```

Loading certificate from file and creating public key
-----------------------------------------------------

``` php
$certificate = new X509Certificate();
$certificate->loadFromFile($crtFilename);

$publicKey = KeyHelper::createPublicKey($certificate);
```


Creating private key
--------------------

``` php
$privateKey = KeyHelper::createPrivateKey($filename, $pass);
```

Signing SAML messages
---------------------
You would require private key for signing SAML messages.

``` php
$message->sign($certificate, $privateKey);
```

Exposing certificate in metadata
--------------------------------

If your SP supports signing you would need to expose your certificate in your SP's metadata entity descriptor

``` php
$ed = new EntityDescriptor();
$sp = new SpSsoDescriptor();
$sp->addKeyDescriptor(new KeyDescriptor('signing', $certificate));
$ed->->addItem($sp);
```
