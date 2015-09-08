METADATA
========

Metadata building
-----------------

Use [lightsaml:sp:meta:build](command_sp_meta_build.md) command for simple cases

``` php
$ed = new EntityDescriptor();
$sp = new SpSsoDescriptor();
$ed->addItem($sp);

// KeyDescriptor
$certificate = new X509Certificate();
$certificate->loadFromFile($certificatePath);
$keyDescriptor = new KeyDescriptor('signing', $certificate);
$ed->addItem($keyDescriptor);

// SingleLogoutService
$s = new SingleLogoutService();
$s->setLocation($url);
$s->setBinding($this->resolveBinding($binding));
$sp->addService($s);

// AssertionConsumerService
$s = new AssertionConsumerService($binding1, $url, 0);
$sp->addService($s);
$s = new AssertionConsumerService($binding2, $url, 1);
$sp->addService($s);
```

Saving metadata to xml
----------------------

``` php
$ed = new Model\Metadata\EntityDescriptor();
// ...
$context = new Meta\SerializationContext();
$ed->getXml($context->getDocument(), $context);
file_put_contents($context->getDocument()->saveXML(), $xml);
```


Loading metadata from xml
-------------------------

``` php
$doc = new \DOMDocument();
$doc->load('sp.xml');
$ed = new EntityDescriptor();
$ed->loadFromXml($doc->firstChild);
```


AUTHN REQUEST
=============

``` php
$spMeta = new Meta\SpMeta();
$spMeta->setNameIdFormat(NameIDPolicy::PERSISTENT);
$spED = getEntityDescriptorSP();
$idpED = getEntityDescriptorIDP();

$builder = new Meta\AuthnRequestBuilder($spED, $idpED, $spMeta);
$request = $builder->build();
```


SENDING MESSAGE
===============

HTTP Redirect
-------------

``` php
$binding = new Binding\HttpRedirect();
$bindingResponse = $binding->send($message);
$bindingResponse->render();
```

HTTP POST
---------

``` php
$binding = new Binding\HttpPost();
$bindingResponse = $binding->send($message);
$bindingResponse->render();
```


RECEIVING MESSAGE
=================

``` php
$request = new Binding\Request();
$request->setQueryString($_SERVER['QUERY_STRING']);
$request->setGet($_GET);
$request->getPost($_POST);
$request->setRequestMethod($_SERVER['REQUEST_METHOD']);

$detector = new BindingDetector();
$binding = $detector->instantiate($this->bindingDetector->getBinding($bindingRequest));

$message = $binding->receive($bindingRequest);
```
