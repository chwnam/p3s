<?php

namespace Chwnam\Saops\Helpers;

class UrlPathHelper
{
    public static function asProjectPath(string $path, string $projectRoot, string $relStr = '$PROJECT_DIR$'): string
    {
        $path = static::expandHome($path);

        if (!str_starts_with($path, $projectRoot)) {
            $path = $projectRoot . '/' . static::getRelativePath($path, $projectRoot);
        }

        return str_replace($projectRoot, $relStr, $path);
    }

    public static function expandHome(string $path): string
    {
        if (str_starts_with($path, '~/')) {
            [, $rest] = explode('/', $path, 2);
            $path = getenv('HOME') . '/' . $rest;
        }

        return $path;
    }

    /**
     * Trim input URL to server info
     *
     * - Extract only host and port number.
     * - Scheme is trimmed out.
     * - If port is 80, it is skipped.
     * - If port is 443, it is included.
     *
     * @param string $server
     *
     * @return string
     */
    public static function asServerInfo(string $server): string
    {
        if (!preg_match(';^(?:(?P<scheme>https?)://)?(?P<host>[^:/]+)(?::(?P<port>\d+))?;', $server, $match)) {
            return '';
        }

        $scheme = $match['scheme'];
        $host   = $match['host'];
        $port   = $match['port'] ?? '';

        if (empty($port)) {
            if ('http' === $scheme) {
                $port = '';
            } elseif ('https' === $scheme) {
                $port = ':443';
            }
        } elseif ('80' === $port) {
            $port = '';
        } else {
            $port = ':' . $port;
        }

        return $host . $port;
    }

    /**
     * Get a relative path from one absolute path to another
     *
     * @param string $path The absolute path to convert to relative
     * @param string $from The base path from which to calculate the relative path
     *
     * @return string The relative path
     */
    public static function getRelativePath(string $path, string $from): string
    {
        // Normalize directory separators and remove trailing slashes
        $path = rtrim(str_replace('\\', '/', $path), '/');
        $from = rtrim(str_replace('\\', '/', $from), '/');

        // Split paths into arrays
        $pathParts = explode('/', $path);
        $fromParts = explode('/', $from);

        // Find common path parts
        $commonLength = 0;
        $length       = min(count($pathParts), count($fromParts));

        while ($commonLength < $length && $pathParts[$commonLength] === $fromParts[$commonLength]) {
            $commonLength++;
        }

        // Build the relative path
        $relativePath = str_repeat('../', count($fromParts) - $commonLength);

        // Add the remaining path parts
        if ($commonLength < count($pathParts)) {
            if (!$relativePath) {
                $relativePath = './';
            }
            $relativePath .= implode('/', array_slice($pathParts, $commonLength));
        }

        return $relativePath ?: '.';
    }

    /**
     * Create a random UUIDv4 string
     *
     * @return string
     */
    public static function getUuid4(): string
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        );
    }

    /**
     * Check if the given path is a Git repository.
     *
     * @param string $path The path to check for a Git repository.
     *
     * @return bool True if the path is a Git repository, false otherwise.
     */
    public static function isGitRepo(string $path): bool
    {
        $gitDir = rtrim($path, '\\/') . '/.git';

        return file_exists($gitDir) && is_dir($gitDir);
    }
}
