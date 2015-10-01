<?php

require_once __DIR__.'/_config.php';

$buildContainer = SpConfig::current()->getBuildContainer();
$builder = new \LightSaml\Builder\Profile\WebBrowserSso\Sp\SsoSpReceiveResponseProfileBuilder($buildContainer);

$context = $builder->buildContext();
$action = $builder->buildAction();

if (SpConfig::current()->debug) {
    var_dump('ACTION TREE');
    var_dump($action->__toString());
}

try {
    $action->execute($context);
} catch (\Exception $ex) {
    var_dump('CONTEXT TREE');
    var_dump($context->__toString());
    throw new \RuntimeException('Error', 0, $ex);
}

var_dump('CONTEXT TREE');
var_dump($context->__toString());

$response = \LightSaml\Context\Profile\Helper\MessageContextHelper::asResponse($context->getInboundContext());

var_dump('RELAY STATE');
var_dump($response->getRelayState());

var_dump('ATTRIBUTES');
foreach ($response->getAllAssertions() as $assertion) {
    foreach ($assertion->getAllAttributeStatements() as $attributeStatement) {
        foreach ($attributeStatement->getAllAttributes() as $attribute) {
            var_dump($attribute);
        }
    }
}

/** @var \LightSaml\Model\Context\DeserializationContext $inboundMessageDeserializationContext */
$inboundMessageDeserializationContext = $context->getPath('inbound_message/deserialization');
$inboundMessageDeserializationContext->getDocument()->formatOutput = true;
var_dump('RECEIVED MESSAGE');
var_dump($inboundMessageDeserializationContext->getDocument()->saveXML());

/** @var \LightSaml\Model\Context\DeserializationContext $decryptedAssertionContext */
$decryptedAssertionContext = $context->getPath('inbound_message/assertion_encrypted_0');
if ($decryptedAssertionContext) {
    $decryptedAssertionContext->getDocument()->formatOutput = true;
    var_dump('DECRYPTED ASSERTION');
    var_dump($decryptedAssertionContext->getDocument()->saveXML());
}
