<?php

require_once __DIR__.'/../autoload.php';

require_once __DIR__.'/how_to_make_authn_request.php';

$serializationContext = new \LightSaml\Model\Context\SerializationContext();

$authnRequest->serialize($serializationContext->getDocument(), $serializationContext);

print $serializationContext->getDocument()->saveXML();
