<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = (new Finder())
  ->in(__DIR__);

return (new Config())
  ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
  ->setRules([
    '@PSR12' => true,
    'class_definition' => [
      'inline_constructor_arguments' => false
    ],
    'array_indentation' => true,
    'method_argument_space' => [
      'on_multiline' => 'ensure_fully_multiline',
      'keep_multiple_spaces_after_comma' => true
    ],
    'array_syntax' => ['syntax' => 'short'],
    'combine_consecutive_unsets' => true,
    'class_attributes_separation' => ['elements' => ['method' => 'one']],
    'multiline_whitespace_before_semicolons' => true,
    'single_quote' => true,
    'braces' => [
      'allow_single_line_closure' => true,
    ],
    'concat_space' => ['spacing' => 'one'],
    'declare_equal_normalize' => true,
    'function_typehint_space' => true,
    'single_line_comment_style' => ['comment_types' => ['hash']],
    'include' => true,
    'lowercase_cast' => true,
    'no_blank_lines_before_namespace' => false,
    'no_extra_blank_lines' => [
      'tokens' => [
        'curly_brace_block',
        'extra',
        'throw',
        'use',
      ],
    ],
    'no_leading_import_slash' => true,
    'no_spaces_around_offset' => true,
    'no_unused_imports' => true,
    'no_whitespace_before_comma_in_array' => true,
    'no_whitespace_in_blank_line' => true,
    'object_operator_without_whitespace' => true,
    'phpdoc_indent' => true,
    'general_phpdoc_tag_rename' => true,
    'phpdoc_no_alias_tag' => true,
    'ternary_operator_spaces' => true,
    'trim_array_spaces' => true,
    'unary_operator_spaces' => true,
    'whitespace_after_comma_in_array' => true,
    'space_after_semicolon' => true,
    'indentation_type' => true,
  ])
  ->setIndent('  ')
  ->setLineEnding("\n")
  ->setFinder($finder);
