<?php

function listFiles($dir, $prefix = '') {
    $files = scandir($dir);

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;

        $path = $dir . DIRECTORY_SEPARATOR . $file;

        echo $prefix . $file . PHP_EOL;

        if (is_dir($path)) {
            listFiles($path, $prefix . '    ');
        }
    }
}

// Start from current directory
listFiles(__DIR__);