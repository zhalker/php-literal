# php-literal

A PHP library that allows using backtick-delimited literals with embedded expressions, similar to JavaScript template literals. This library processes PHP code and enables dynamic inclusion of files with expressions inside backticks.

## Installation

You can install `php-literal` via Composer:

```bash
composer require zhalker/php-literal
```

## Example Usage

```php
<?php
$name = `Zhalker`;

$list = [
    `apple`,
    `banana`,
    `cherry`
];

function salute($name) {
    return `Im "{$name}"`;
}

echo `Hello!!\n {salute($name)}`;

echo "\nList of fruits:\n";

foreach ($list as $key => $value) {
    echo `<div><span>Item {$key}: </span><span>{$value}</span></div>`;
}

```

```php
<?php //Main.php
use function PHPLiteral\PHPLiteral;

// Example:
include_once PHPLiteral("path/to/your/file.php");

```
```php
// Output raw
Hello!!
 Im "Zhalker"
List of fruits:

    <div><span>Item 0: </span><span>apple</span></div>
    
    <div><span>Item 1: </span><span>banana</span></div>
    
    <div><span>Item 2: </span><span>cherry</span></div>
```

```php
//Output rendering

Hello!! Im "Zhalker" List of fruits:
Item 0: apple
Item 1: banana
Item 2: cherry
```

## How It Works

- **Backtick literals**: Strings delimited by backticks (\`\`) are parsed and evaluated, similar to JavaScript template literals.
- **Expressions**: Embedded expressions inside curly braces (`{$expression}`) are evaluated and replaced at runtime.

## License

This library is licensed under the [MIT License](LICENSE).

## Contributing

If you find any bugs or want to improve the library, feel free to open an issue or submit a pull request. Contributions are welcome!

---
