<?php

declare(strict_types=1);

use PhpCsFixer\Finder;
use PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer;

$finder = Finder::create()
    ->in('src');

return (new PhpCsFixer\Config)
    ->setUsingCache(false)
    ->setRiskyAllowed(true)
    ->setRules([
        'blank_line_before_statement' => [
            'statements' => [
                'break',
                'continue',
                'return',
                'throw',
                'try'
            ]
        ],
        '@PSR12' => true,
        'native_function_invocation' => [
            'include' => [NativeFunctionInvocationFixer::SET_ALL],
            'scope' => 'namespaced',
            'strict' => true,
        ],
        '@PHP80Migration:risky' => true,
        'concat_space' => [
            'spacing' => 'one',
        ],
        'strict_param' => true,
        'mb_str_functions' => true,
        'declare_strict_types' => true,
        'array_syntax' => [
            'syntax' => 'short'
        ],
        'class_definition' => [
            'multi_line_extends_each_single_line' => true,
        ],
        'phpdoc_types_order' => [
            'null_adjustment' => 'none',
        ],
        'ordered_imports' => false,
        'no_superfluous_phpdoc_tags' => [
            'allow_mixed' => true,
            'remove_inheritdoc' => true,
        ],
        'single_line_throw' => false,
        'array_indentation' => true,
        'compact_nullable_typehint' => true,
        'single_trait_insert_per_statement' => false,
        'phpdoc_align' => [
            'align' => 'vertical'
        ],
        'no_blank_lines_after_class_opening' => true,
    ])
    ->setFinder($finder);
