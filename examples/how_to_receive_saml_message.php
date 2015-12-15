<?php

require_once __DIR__.'/../autoload.php';

$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

$bindingFactory = new \LightSaml\Binding\BindingFactory();
$binding = $bindingFactory->getBindingByRequest($request);

$messageContext = new \LightSaml\Context\Profile\MessageContext();
/** @var \LightSaml\Model\Protocol\Response $response */
$response = $binding->receive($request, $messageContext);

print $response->getID();
