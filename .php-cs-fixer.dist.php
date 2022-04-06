<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

$header = <<<'EOF'
Ratepay EVA - Evidence API

This document contains trade secret data which are the property of
Ratepay GmbH, Berlin, Germany. Information contained herein must not be used,
copied or disclosed in whole or part unless permitted in writing by Ratepay GmbH.
All rights reserved by Ratepay GmbH.

Copyright (c) 2021 Ratepay GmbH / Berlin / Germany
EOF;

$rules = [
    '@PSR12' => true,
    'strict_param' => true,
    'declare_strict_types' => true,
    'array_syntax' => ['syntax' => 'short'],
//    'header_comment' => ['header' => $header],
    'list_syntax' => ['syntax' => 'short'],
    'no_unused_imports' => true,
];

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules($rules)
    ->setFinder($finder);
