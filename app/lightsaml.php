#!/usr/bin/env php
<?php

require_once __DIR__ . '/../autoload.php';

$app = new \Symfony\Component\Console\Application('lightsaml', '1.0');
$input = new \Symfony\Component\Console\Input\ArgvInput();

$app->add(new \LightSaml\Command\BuildSPMetadataCommand());
$app->run($input);
