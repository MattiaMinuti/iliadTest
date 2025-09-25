<?php

/**
 * PHP CS Fixer Configuration for Gestionale Iliad.
 *
 * This configuration enforces PSR-12 coding standards
 * and ensures consistent code formatting across the project.
 */

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/app')
    ->in(__DIR__ . '/tests')
    ->name('*.php')
    ->exclude([
        'storage',
        'vendor',
        'bootstrap/cache',
    ]);

return (new PhpCsFixer\Config())
    ->setRules([
        // PSR-12 standard
        '@PSR12' => true,

        // Array syntax
        'array_syntax' => ['syntax' => 'short'],

        // Import statements
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'no_unused_imports' => true,

        // Spacing
        'not_operator_with_successor_space' => true,
        'unary_operator_spaces' => true,
        'binary_operator_spaces' => true,
        'trailing_comma_in_multiline' => true,

        // PHP Doc
        'phpdoc_scalar' => true,
        'phpdoc_align' => true,
        'phpdoc_summary' => true,

        // General formatting
        'concat_space' => ['spacing' => 'one'],
        'method_chaining_indentation' => true,
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],

        // Control structures
        'control_structure_braces' => true,
        'control_structure_continuation_position' => true,

        // Function and method formatting
        'function_declaration' => true,
        'method_argument_space' => true,

        // Class formatting
        'class_attributes_separation' => [
            'elements' => [
                'method' => 'one',
                'property' => 'one',
            ],
        ],

        // String formatting
        'single_quote' => true,
        'escape_implicit_backslashes' => true,

        // Other rules
        'no_extra_blank_lines' => [
            'tokens' => [
                'curly_brace_block',
                'extra',
                'parenthesis_brace_block',
                'square_brace_block',
                'throw',
                'use',
            ],
        ],
        'no_trailing_whitespace' => true,
        'no_trailing_whitespace_in_comment' => true,
        'no_whitespace_in_blank_line' => true,
    ])
    ->setFinder($finder)
    ->setCacheFile(__DIR__ . '/.php-cs-fixer.cache')
    ->setRiskyAllowed(false);
