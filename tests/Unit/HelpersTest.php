<?php

use Chwnam\Saops\Helpers\UrlPathHelper;

it('tests UrlPathHelper::asServerInfo', function ($expected, $input) {
    expect(UrlPathHelper::asServerInfo($input))->toBe($expected);
})->with([
    'plain server info'           => ['my.localhost', 'my.localhost'],
    'plain server info with port' => ['my.localhost:8080', 'my.localhost:8080'],
    'plain server info with 80'   => ['my.localhost', 'my.localhost:80'],
    'plain server info with 443'  => ['my.localhost:443', 'my.localhost:443'],
    'http server info'            => ['my.localhost', 'http://my.localhost'],
    'http server info with 80'    => ['my.localhost', 'http://my.localhost:80'],
    'http server info with 443'   => ['my.localhost:443', 'https://my.localhost'],
    'https server info with port' => ['my.localhost:8080', 'http://my.localhost:8080'],
]);


it('tests UrlPathHelper::getRelativePath', function ($path, $from, $expected) {
    expect(UrlPathHelper::getRelativePath($path, $from))->toBe($expected);
})->with([
    [
        'path'     => '/var/www/html/project/public',
        'from'     => '/var/www/html/project',
        'expected' => './public',
    ],
    [
        'path'     => '/var/www/html/project/src/components',
        'from'     => '/var/www/html/project/public/assets/js',
        'expected' => '../../../src/components',
    ],
    [
        'path'     => '/home/user/project/wordpress/libs/custom-directory/custom.dic',
        'from'     => '/home/user/project/wordpress/foo',
        'expected' => '../libs/custom-directory/custom.dic',
    ],
]);