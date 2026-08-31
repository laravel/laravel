<?php

require __DIR__ . '/tools/php-cs-fixer.d/PhpdocSingleLineVarFixer.php';

$header = file_get_contents(__DIR__ . '/tools/php-cs-fixer.d/header.txt');

return (new PhpCsFixer\Config())
    ->registerCustomFixers([
        new \PharIo\CSFixer\PhpdocSingleLineVarFixer()
    ])
    ->setRiskyAllowed(true)
    ->setRules(
        [
            'PharIo/phpdoc_single_line_var_fixer'           => true,

            'align_multiline_comment'                       => true,
            'array_indentation'                             => true,
            'array_syntax'                                  => ['syntax' => 'short'],
            'binary_operator_spaces'                        => [
                'operators' => [
                    '='  => 'align',
                    '=>' => 'align',
                ],
            ],
            'blank_line_after_namespace'                    => true,
            'blank_line_after_opening_tag'                  => false,
            'blank_line_before_statement'                   => [
                'statements' => [
                    'break',
                    'continue',
                    'declare',
                    'do',
                    'for',
                    'foreach',
                    'if',
                    'include',
                    'include_once',
                    'require',
                    'require_once',
                    'return',
                    'switch',
                    'throw',
                    'try',
                    'while',
                    'yield',
                ],
            ],
            'braces'                                        => [
                'allow_single_line_closure'                   => false,
                'position_after_anonymous_constructs'         => 'same',
                'position_after_control_structures'           => 'same',
                'position_after_functions_and_oop_constructs' => 'same'
            ],
            'cast_spaces'                                   => ['space' => 'none'],

            // This fixer removes the blank line at class start, no way to disable that, so we disable the fixer :(
            //'class_attributes_separation'                   => ['elements' => ['const', 'method', 'property']],

            'combine_consecutive_issets'                    => true,
            'combine_consecutive_unsets'                    => true,
            'compact_nullable_typehint'                     => true,
            'concat_space'                                  => ['spacing' => 'one'],
            'date_time_immutable'                           => true,
            'declare_equal_normalize'                       => ['space' => 'single'],
            'declare_strict_types'                          => true,
            'dir_constant'                                  => true,
            'elseif'                                        => true,
            'encoding'                                      => true,
            'full_opening_tag'                              => true,
            'fully_qualified_strict_types'                  => true,
            'function_declaration'                          => [
                'closure_function_spacing' => 'one'
            ],
            'global_namespace_import'                       => [
                'import_classes'   => true,
                'import_constants' => true,
                'import_functions' => true,
            ],
            'header_comment'                                => ['header' => $header, 'separate' => 'none'],
            'indentation_type'                              => true,
            'is_null'                                       => true,
            'line_ending'                                   => true,
            'list_syntax'                                   => ['syntax' => 'short'],
            'logical_operators'                             => true,
            'lowercase_cast'                                => true,
            'constant_case'                                 => ['case' => 'lower'],
            'lowercase_keywords'                            => true,
            'lowercase_static_reference'                    => true,
            'magic_constant_casing'                         => true,
            'method_argument_space'                         => ['on_multiline' => 'ensure_fully_multiline'],
            'modernize_types_casting'                       => true,
            'multiline_comment_opening_closing'             => true,
            'multiline_whitespace_before_semicolons'        => true,
            'new_with_braces'                               => false,
            'no_alias_functions'                            => true,
            'no_alternative_syntax'                         => true,
            'no_blank_lines_after_class_opening'            => false,
            'no_blank_lines_after_phpdoc'                   => true,
            'no_blank_lines_before_namespace'               => true,
            'no_closing_tag'                                => true,
            'no_empty_comment'                              => true,
            'no_empty_phpdoc'                               => true,
            'no_empty_statement'                            => true,
            'no_extra_blank_lines'                          => true,
            'no_homoglyph_names'                            => true,
            'no_leading_import_slash'                       => true,
            'no_leading_namespace_whitespace'               => true,
            'no_mixed_echo_print'                           => ['use' => 'print'],
            'no_multiline_whitespace_around_double_arrow'   => true,
            'no_null_property_initialization'               => true,
            'no_php4_constructor'                           => true,
            'no_short_bool_cast'                            => true,
            'echo_tag_syntax'                               => ['format' => 'long'],
            'no_singleline_whitespace_before_semicolons'    => true,
            'no_spaces_after_function_name'                 => true,
            'no_spaces_inside_parenthesis'                  => true,
            'no_superfluous_elseif'                         => true,
            'no_superfluous_phpdoc_tags'                    => true,
            'no_trailing_comma_in_list_call'                => true,
            'no_trailing_comma_in_singleline_array'         => true,
            'no_trailing_whitespace'                        => true,
            'no_trailing_whitespace_in_comment'             => true,
            'no_unneeded_control_parentheses'               => false,
            'no_unneeded_curly_braces'                      => false,
            'no_unneeded_final_method'                      => true,
            'no_unreachable_default_argument_value'         => true,
            'no_unset_on_property'                          => true,
            'no_unused_imports'                             => true,
            'no_useless_else'                               => true,
            'no_useless_return'                             => true,
            'no_whitespace_before_comma_in_array'           => true,
            'no_whitespace_in_blank_line'                   => true,
            'non_printable_character'                       => true,
            'normalize_index_brace'                         => true,
            'object_operator_without_whitespace'            => true,
            'ordered_class_elements'                        => [
                'order' => [
                    'use_trait',
                    'constant_public',
                    'constant_protected',
                    'constant_private',
                    'property_public_static',
                    'property_protected_static',
                    'property_private_static',
                    'property_public',
                    'property_protected',
                    'property_private',
                    'method_public_static',
                    'construct',
                    'destruct',
                    'magic',
                    'phpunit',
                    'method_public',
                    'method_protected',
                    'method_private',
                    'method_protected_static',
                    'method_private_static',
                ],
            ],
            'ordered_imports' => [
                'imports_order' => [
                    PhpCsFixer\Fixer\Import\OrderedImportsFixer::IMPORT_TYPE_CLASS,
                    PhpCsFixer\Fixer\Import\OrderedImportsFixer::IMPORT_TYPE_CONST,
                    PhpCsFixer\Fixer\Import\OrderedImportsFixer::IMPORT_TYPE_FUNCTION,
                ]
            ],
            'phpdoc_add_missing_param_annotation'           => true,
            'phpdoc_align'                                  => true,
            'phpdoc_annotation_without_dot'                 => true,
            'phpdoc_indent'                                 => true,
            'phpdoc_no_access'                              => true,
            'phpdoc_no_empty_return'                        => true,
            'phpdoc_no_package'                             => true,
            'phpdoc_order'                                  => true,
            'phpdoc_return_self_reference'                  => true,
            'phpdoc_scalar'                                 => true,
            'phpdoc_separation'                             => true,
            'phpdoc_single_line_var_spacing'                => true,
            'phpdoc_to_comment'                             => true,
            'phpdoc_trim'                                   => true,
            'phpdoc_trim_consecutive_blank_line_separation' => true,
            'phpdoc_types'                                  => ['groups' => ['simple', 'meta']],
            'phpdoc_types_order'                            => true,
            'phpdoc_to_return_type'                         => true,
            'phpdoc_var_without_name'                       => true,
            'pow_to_exponentiation'                         => true,
            'protected_to_private'                          => true,
            'return_assignment'                             => true,
            'return_type_declaration'                       => ['space_before' => 'none'],
            'self_accessor'                                 => false,
            'semicolon_after_instruction'                   => true,
            'set_type_to_cast'                              => true,
            'short_scalar_cast'                             => true,
            'simplified_null_return'                        => true,
            'single_blank_line_at_eof'                      => true,
            'single_import_per_statement'                   => true,
            'single_line_after_imports'                     => true,
            'single_quote'                                  => true,
            'standardize_not_equals'                        => true,
            'ternary_to_null_coalescing'                    => true,
            'trailing_comma_in_multiline'                   => false,
            'trim_array_spaces'                             => true,
            'unary_operator_spaces'                         => true,
            'visibility_required'                           => [
                'elements' => [
                    'const',
                    'method',
                    'property',
                ],
            ],
            'void_return'                                   => true,
            'whitespace_after_comma_in_array'               => true,
            'yoda_style'                                    => false
        ]
    )
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->files()
            ->in(__DIR__ . '/build')
            ->in(__DIR__ . '/src')
            ->in(__DIR__ . '/tests')
            ->notName('autoload.php')
    );
