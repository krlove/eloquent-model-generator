<?php

// vim: expandtab syntax=php

declare(strict_types=1);

$finder = Symfony\Component\Finder\Finder::create()
    ->exclude('tests/integration/data')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true)
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->name('*.php');
;

$config = new PhpCsFixer\Config();
$config
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        '@PSR2'  => true,
        //'@PhpCsFixer:risky'     => true,
        'align_multiline_comment' => true,
        'array_indentation'       => true,

        'array_syntax' => [
            'syntax' => 'short',
        ],

        'binary_operator_spaces' => [
            'default' => 'align_single_space_minimal',
        ],

        'blank_line_after_namespace' => true,

        'blank_line_before_statement' => [
            'statements' => [
                //'break',
                //'continue',
                'declare',
                'return',
                //'throw',
                'switch',
                'try',
            ],
        ],

        'clean_namespace' => true,

        'concat_space' => [
            'spacing' => 'one',
        ],

        'method_argument_space' => [
            'on_multiline'                     => 'ensure_fully_multiline',
            'keep_multiple_spaces_after_comma' => true,
        ],

        'no_break_comment'                  => false,
        'no_empty_comment'                  => true,
        'no_superfluous_phpdoc_tags'        => false,
        'no_unused_imports'                 => true,
        'not_operator_with_successor_space' => true,

        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
        ],

        'phpdoc_align'                   => true,
        'phpdoc_scalar'                  => true,
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_summary'                 => false,
        'phpdoc_var_without_name'        => true,

        'phpdoc_types_order' => [
            'null_adjustment' => 'always_last',
            'sort_algorithm'  => 'none',
        ],

        //'single_line_comment_spacing' => true,
        //'single_line_comment_style'   => true,

        'trailing_comma_in_multiline' => [
            'elements' => [
                'arrays',
            ],
        ],

        'unary_operator_spaces' => true,
    ])
    ->setFinder($finder);

return $config;
