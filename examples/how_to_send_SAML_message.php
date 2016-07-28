<?php

require_once __DIR__.'/../autoload.php';

$authnRequest = new \LightSaml\Model\Protocol\AuthnRequest();
$authnRequest->setDestination('http://destination.com');

$bindingFactory = new \LightSaml\Binding\BindingFactory();

// REDIRECT BINDING
$redirectBinding = $bindingFactory->create(\LightSaml\SamlConstants::BINDING_SAML2_HTTP_REDIRECT);
$messageContext = new \LightSaml\Context\Profile\MessageContext();
$messageContext->setMessage($authnRequest);
/** @var \Symfony\Component\HttpFoundation\RedirectResponse $httpResponse */
$httpResponse = $redirectBinding->send($messageContext);
print $httpResponse->getTargetUrl()."\n\n";
/*
http://destination.com?SAMLRequest=s7GvyM1RKEstKs7Mz7NVMtQzULK347JxLC3JyAtKLSxNLS5RAKrIK7ZVKi3Ks8pPLM4stspLzE0ttipJtgp29PWxMtIzsCooyi%2FJT87PUVIIgxkFFFZScAHqz8xLLAGLZJSUFFjp66cgxPSS83OV9O24AA%3D%3D
*/


// POST BINDING
$postBinding = $bindingFactory->create(\LightSaml\SamlConstants::BINDING_SAML2_HTTP_POST);
$messageContext = new \LightSaml\Context\Profile\MessageContext();
$messageContext->setMessage($authnRequest);
/** @var \Symfony\Component\HttpFoundation\Response $httpResponse */
$httpResponse = $postBinding->send($messageContext);
print $httpResponse->getContent()."\n\n";
/*
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>POST data</title>
</head>
<body onload="document.getElementsByTagName('input')[0].click();">

    <noscript>
        <p><strong>Note:</strong> Since your browser does not support JavaScript, you must press the button below once to proceed.</p>
    </noscript>

    <form method="post" action="http://destination.com">
        <input type="submit" style="display:none;" />

        <input type="hidden" name="SAMLRequest" value="PD94bWwgdmVyc2lvbj0iMS4wIj8+CjxBdXRoblJlcXVlc3QgeG1sbnM9InVybjpvYXNpczpuYW1lczp0YzpTQU1MOjIuMDpwcm90b2NvbCIgVmVyc2lvbj0iMi4wIiBEZXN0aW5hdGlvbj0iaHR0cDovL2Rlc3RpbmF0aW9uLmNvbSIvPgo=" />

        <noscript>
            <input type="submit" value="Submit" />
        </noscript>

    </form>
</body>
</html>
*/
