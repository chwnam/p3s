<?php

namespace Chwnam\Saops\Presets;

use Chwnam\Saops\Helpers\UrlPathHelper;
use Exception;

class WordPress
{
    /**
     * Fetch server information based on WordPress and WP-CLI
     *
     * @param string $setup
     *
     * @return string
     * @throws Exception
     */
    public static function getServerInfo(string $setup): string
    {
        $output = '';

        if (!preg_match('/^preset:wordpress{(.+)}$/', $setup, $matches)) {
            return '';
        }

        $wpCli = $matches[1];
        if (!is_executable($wpCli)) {
            throw new Exception("'$wpCli' is not executable.");
        }

        exec("$wpCli option get siteurl", $stdout);
        if ($stdout) {
            $output = UrlPathHelper::asServerInfo(trim($stdout[0]));
        }

        return $output;
    }

    /**
     * Check if the path is WordPress
     *
     * @param string $path
     *
     * @return bool
     */
    public static function isWordPress(string $path): bool
    {
        $indexContent = file_get_contents($path . '/index.php') ?: '';

        $validIndex =
            str_contains($indexContent, "define( 'WP_USE_THEMES', true );") &&
            str_contains($indexContent, "require __DIR__ . '/wp-blog-header.php';");

        $configFound =
            file_exists($path . '/wp-config.php') ||
            file_exists(dirname($path) . '/wp-config.php');

        return $validIndex && $configFound;
    }

    public static function isPlugin(string $path): bool
    {
        return str_contains($path, '/wp-content/plugins/');
    }

    public static function isTheme(string $path): bool
    {
        return str_contains($path, '/wp-content/themes/');
    }
}
