<?php

$finder = PhpCsFixer\Finder::create()
    ->in('src')
    ->exclude('vendor')
;

$config = new PhpCsFixer\Config();
    return $config->setRules([
        '@Symfony' => true,
        // Keep the existing 5.5 style across PHP CS Fixer versions.
        'no_useless_else' => false,
        'full_opening_tag' => false,
        'phpdoc_separation' => false,
        'global_namespace_import' => ['import_classes' => true]
    ])
    ->setFinder($finder)
;