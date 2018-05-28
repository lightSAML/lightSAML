<?php

$finder = PhpCsFixer\Finder::create()
    ->in('src')
;

$header = <<<EOT
This file is part of the LightSAML-Core package.

(c) Milos Tomic <tmilos@lightsaml.com>

This source file is subject to the MIT license that is bundled
with this source code in the file LICENSE.
EOT;

return PhpCsFixer\Config::create()
    ->setRules(array(
        '@Symfony' => true,
        'simplified_null_return' => false,
        'phpdoc_no_empty_return' => false,
        'no_mixed_echo_print' => ['use' => 'print'],
        'header_comment' => ['header' => $header],
    ))
    ->setUsingCache(false)
    ->setFinder($finder)
;
