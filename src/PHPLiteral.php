<?php

namespace PHPLiteral;

use DumbContextualParser\ContextualReplaceText;

/**
 * Includes a PHP file, allowing use of backtick-delimited literal strings
 * with embedded expressions, similar to JavaScript template literals.
 *
 * @param string $path Path to the PHP file to include.
 * @param bool $strict_mode If true, preserves newlines and escapes characters literally.
 * @return string The result of the included file.
 */
function PHPLiteral(string $path): string {

    if (!file_exists($path)) {
        return 'error file not found: ' . $path;
    }

    $raw = file_get_contents($path);

    $rules = [
        [
            'scope_start' => '<?php',
            'scope_end'   => '?>',
            'self_replace' => [
                'block' => [
                    'open' => '```',
                    'close' => '```',
                    'pattern' => 'echo "%s"'
                ]
            ],
            'inner_scopes' => [
                [
                    'scope_start' => '```',
                    'scope_end'   => '```',
                    'self_replace' => [
                        'block' => [
                            'open' => '{',
                            'close' => '}',
                            'pattern' => '".(%s)."'
                        ]

                    ]
                ]
            ]
        ],
        [
            'scope_start' => '<?php',
            'scope_end'   => '?>',
            'self_replace' => [
                'block' => [
                    'open' => '`',
                    'close' => '`',
                    'pattern' => '"%s"'
                ]
            ],
            'inner_scopes' => [
                [
                    'scope_start' => '`',
                    'scope_end'   => '`',
                    'self_replace' => [
                        'block' => [
                            'open' => '"',
                            'close' => '"',
                            'pattern' => function ($inner) {
                                return sprintf('\x22%s\x22', $inner);
                            }
                        ],
                        'token' => [
                            'search' => '/(?<!\\\\)"/',
                            'subject' => '\x22'
                        ]
                    ]
                ],
                [
                    'scope_start' => '`',
                    'scope_end'   => '`',
                    'self_replace' => [
                        'block' => [
                            'open' => '{',
                            'close' => '}',
                            'pattern' => '".(%s)."'
                        ]
                    ]
                ]
            ]
        ]
    ];

    $output = ContextualReplaceText::applyContexts($raw, $rules);

    return tempPathFromString($output, $path);
}

/**
 * Writes the given PHP code to a temporary file and return the path.
 * The file is automatically deleted at the end of script execution.
 *
 * @param string $code The PHP code to execute.
 * @param string $originalPath Path of the original file (used in temp file naming).
 * @return string The path of the temporary file.
 */
function tempPathFromString(string $code, string $originalPath): string {
    // Add a prefix with the original path for easier error tracking
    $prefix = 'path_' . md5($originalPath) . '_';
    $tempFile = tempnam(sys_get_temp_dir(), $prefix);

    // Ensure .php extension for cross-platform consistency
    $phpTempFile = $tempFile . '.php';
    rename($tempFile, $phpTempFile);

    file_put_contents($phpTempFile, $code);

    // Automatically delete the file on script shutdown
    register_shutdown_function(function () use ($phpTempFile) {
        if (file_exists($phpTempFile)) {
            unlink($phpTempFile);
        }
    });

    return $phpTempFile;
}
