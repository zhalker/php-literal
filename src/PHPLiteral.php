<?php

namespace PHPLiteral;

/**
 * Includes a PHP file, allowing use of backtick-delimited literal strings
 * with embedded expressions, similar to JavaScript template literals.
 *
 * @param string $path Path to the PHP file to include.
 * @param bool $strict_mode If true, preserves newlines and escapes characters literally.
 * @return mixed The result of the included file.
 */
function PHPLiteral(string $path, bool $strict_mode = true): mixed {
    if (!file_exists($path)) {
        return '';
    }

    $tokens = token_get_all(file_get_contents($path));
    $output = '';
    $in_backtick = false;
    $backtick_content = '';

    foreach ($tokens as $token) {
        if (is_array($token)) {
            if (!$in_backtick) {
                $output .= $token[1];
            } else {
                $backtick_content .= $token[1];
            }
        } else {
            if ($token === '`') {
                if (!$in_backtick) {
                    $in_backtick = true;
                    $backtick_content = '';
                } else {
                    $in_backtick = false;
                    $converted = processBacktickContent($backtick_content, $strict_mode);
                    $output .= "\"$converted\"";
                }
            } else {
                if (!$in_backtick) {
                    $output .= $token;
                } else {
                    $backtick_content .= $token;
                }
            }
        }
    }

    return tempPathFromString($output, $path);
}

/**
 * Processes the content within backticks, converting embedded expressions
 * and handling escape sequences depending on the selected mode.
 *
 * @param string $content The content inside backticks.
 * @param bool $strict_mode Whether to treat content in strict (literal) mode.
 * @return string The processed content ready for evaluation.
 */
function processBacktickContent(string $content, bool $strict_mode = true): string {
    if ($strict_mode) {
        // In strict mode: double backslashes and escape double quotes for \n \r \t
        $escaped_content = str_replace(['\\n', '\\r', '\\t'], ['\\\\n', '\\\\r', '\\\\t'], $content);
        $escaped_content = str_replace('"', '\"', $escaped_content);
    } else {
        // In non-strict mode:
        // 1. Keep backslashes as is
        $escaped_content = $content;
        // 2. Remove actual newlines [\r,\n,\t]
        $escaped_content = str_replace(["\n", "\r", "\t"], '', $escaped_content);
        // 3. Replace literal \n and \r with real newlines
        $escaped_content = str_replace(['\\n', '\\r', '\\t'], ["\n", "\r", '\\t'], $escaped_content);
        // 4. Re-escaped
        $escaped_content = preg_replace('/(?<!\\\\)(\\\\)"/', '\\\\\1\"', $escaped_content);
        // 5. Escape double quotes
        $escaped_content = preg_replace('/(?<!\\\\)"/', '\\"', $escaped_content);
    }

    // Replace unescaped {expr} with PHP expressions
    $converted = preg_replace_callback('/(?<!\\\\)\{(.*?)\}/s', function ($matches) {
        return '" . ' . preg_replace('/(?<!\\\\)\\\\(")/', '$1', $matches[1]) . ' . "';
    }, $escaped_content);

    // Restore escaped curly braces \{ and \}
    $converted = str_replace(['\\{', '\\}'], ['{', '}'], $converted);

    return $converted;
}

/**
 * Writes the given PHP code to a temporary file and return the path.
 * The file is automatically deleted at the end of script execution.
 *
 * @param string $code The PHP code to execute.
 * @param string $original_path Path of the original file (used in temp file naming).
 * @return string The path of the temporary file.
 */
function tempPathFromString(string $code, string $original_path): mixed {
    // Add a prefix with the original path for easier error tracking
    $prefix = 'path:' . $original_path . '___';
    $temp_file = tempnam(sys_get_temp_dir(), $prefix);
    file_put_contents($temp_file, $code);

    // Automatically delete the file on script shutdown
    register_shutdown_function(function () use ($temp_file) {
        if (file_exists($temp_file)) {
            unlink($temp_file);
        }
    });

    return $temp_file;
}
