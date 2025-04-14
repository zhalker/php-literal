<?php
$name = `Zhalker`;

function salute($name) {
    return `Im {$name}`;
}

echo `Hello!!\n

{salute($name)}`;
