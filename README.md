# php-literal

A PHP library that allows using backtick-delimited literals with embedded expressions, similar to JavaScript template literals. This library processes PHP code and enables dynamic inclusion of files with expressions inside backticks.

## Installation

You can install `php-literal` via Composer:

```bash
composer require zhalker/php-literal
```

## Example Usage

```php
<?php //file.php
$name = `Zhalker`;

function salute($name) {
    return `Im {$name}`;
}

echo `Hello!!\n

{salute($name)}`;

```

```php
<?php //Main.php
use function PHPLiteral\PHPLiteral;

// Example 1: Multi-line strings are allowed, and line breaks and tabs are kept as strings.
include_once PHPLiteral("path/to/your/file.php");

// Example 2: There are no multi-line strings and line breaks and tabs are resolved.
include_once PHPLiteral("path/to/your/file.php", false);

```

```php
//Output 1:
Hello!!\n

Im Zhalker

//Output 2:
Hello!!
Im Zhalker
```
## Sandbox

- [Paiza.io](https://paiza.io/projects/Drfk_-SAZDMXAtUm7MUfow)

## How It Works

- **Backtick literals**: Strings delimited by backticks (\`\`) are parsed and evaluated, similar to JavaScript template literals.
- **Expressions**: Embedded expressions inside curly braces (`{$expression}`) are evaluated and replaced at runtime.
- **Escape sequences**: Handles escape sequences such as `\n` for newlines, `\r` for carriage return, and `\t` for tab.
- **Strict mode**: In strict mode, it is possible to use character strings longer than one line and explicit line breaks are kept intact. In non-strict mode, line breaks and explicit tabs such as (`/n`,`/r`,`/t`) are resolved.

## License

This library is licensed under the [MIT License](LICENSE).

## Contributing

If you find any bugs or want to improve the library, feel free to open an issue or submit a pull request. Contributions are welcome!

---
