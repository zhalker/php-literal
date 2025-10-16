<?php
// Load composer's autoloader from the project root regardless of current working dir
require_once __DIR__ . '/../vendor/autoload.php';

use function PHPLiteral\PHPLiteral;

// Resolve the template path relative to this test file
require_once PHPLiteral(__DIR__ . '/raw.php');
