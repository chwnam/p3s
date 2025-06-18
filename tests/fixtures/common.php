<?php

function getCommonJson(): array
{
    $projectRoot = getTestPath('/fixtures/fake.wordpress');
    $target      = $projectRoot . '/wp-content/plugins/fake-plugin-1';

    return [
        'version'     => '1.0',
        'projectRoot' => $projectRoot,
        'target'      => $target,
        'setup'       => [],
    ];
}

function getTestPath(string $path): string
{
    return dirname(__DIR__) . '/' . trim($path, '\\/');
}
