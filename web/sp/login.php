<?php

$idpEntityId = $_REQUEST['idp'];
if (null == $idpEntityId) {
    header('Location: discovery.php');
    exit;
}

require_once __DIR__.'/_config.php';

$buildContainer = SpConfig::current()->getBuildContainer();
$builder = new \LightSaml\Builder\Profile\WebBrowserSso\Sp\SsoSpSendAuthnRequestProfileBuilder($buildContainer, $idpEntityId);

$buildContainer->getSystemContainer()->getEventDispatcher()
    ->addListener(
        \LightSaml\Event\BindingMessageSent::NAME,
        function (\LightSaml\Event\BindingMessageSent $event) {
//            var_dump($event->getMessageString());
//            exit;
        }
    );
$buildContainer->getSystemContainer()->getEventDispatcher()
    ->addListener(
        \LightSaml\Event\BeforeEncrypt::NAME,
        function (\LightSaml\Event\BeforeEncrypt $event) {
            /** @var \LightSaml\Context\Profile\ProfileContext $context */
            $context = $event->getContext();
            $context->getOutboundMessage()->setRelayState('relayState');
        }
    );

$context = $builder->buildContext();
$action = $builder->buildAction();
$action->map(function (\LightSaml\Action\ActionInterface $action) use ($buildContainer) {
    return new \LightSaml\Action\LoggableAction($action, $buildContainer->getSystemContainer()->getLogger());
});

$action->execute($context);

$context->getHttpResponseContext()->getResponse()->send();
