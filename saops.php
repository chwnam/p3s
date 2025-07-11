<?php

require_once __DIR__ . '/vendor/autoload.php';

const P3S_NAME        = 'SAOPS';
const P3S_DESCRIPTION = 'Settings Automation Of PhpStorm';
const P3S_VERSION     = '1.0.0-beta.3';

function help(): void
{
    echo P3S_NAME . ' - ' . P3S_DESCRIPTION . PHP_EOL;
}

function version(): void
{
    echo P3S_VERSION . PHP_EOL;
}

if ('cli' !== php_sapi_name()) {
    die('This script can only be run from the command line.');
}

$opts = getopt('chv');

if (isset($opts['h'])) {
    help();
    exit;
}

if (isset($opts['v'])) {
    version();
    exit;
}

try {
    $configPath = $opts['c'] ?? __DIR__ . '/config.json';
    (new Chwnam\Saops\SettingsAutomationOfPhpStorm($configPath))->run();
} catch (Exception $e) {
    die($e->getMessage());
}
