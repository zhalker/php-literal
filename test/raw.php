<?php
$name = `Zhalker`;

$list = [
    `apple`,
    `banana`,
    `cherry`
];

function salute($name) {
    return `Im {$name}`;
}

echo `Hello!!\n {salute($name)}`;

echo "\nList of fruits:\n";

foreach ($list as $key => $value) {
    ```
    <div><span>Item {$key}: </span><span>{$value}</span></div>
    ```;
}
