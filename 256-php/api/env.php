<?php

$envFile = __DIR__ . '/.env';

if (!file_exists($envFile)) {
    throw new Exception('.env file not found');
}

$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($lines as $line) {
    if (str_starts_with(trim($line), '#')) continue;

    [$key, $value] = explode('=', $line, 2);

    $key = trim($key);
    $value = trim($value);

    // remove quotes if exist
    $value = trim($value, "\"'");

    putenv("$key=$value");
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}
