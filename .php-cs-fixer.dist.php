<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('var')
    ->exclude('vendor')
    ->exclude('node_modules')
    ->in(__DIR__);

$config = new PhpCsFixer\Config();
return $config->setRules([
        '@Symfony' => true,
        'yoda_style' => ['equal' => false, 'identical' => false],
        'increment_style' => ['style' => 'post'],
		'single_quote' => false,
    ])
    ->setIndent("\t")
    ->setFinder($finder);
