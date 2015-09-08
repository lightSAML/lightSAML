<?php

require_once __DIR__.'/_config.php';

$builder = new \LightSaml\Builder\Profile\Metadata\MetadataProfileBuilder(
    SpConfig::current()->getBuildContainer()
);

$context = $builder->buildContext();
$action = $builder->buildAction();

//print "<pre>\n";
//print_r($action->debugPrintTree());
//
//exit;

$action->execute($context);

$context->getHttpResponseContext()->getResponse()->send();
