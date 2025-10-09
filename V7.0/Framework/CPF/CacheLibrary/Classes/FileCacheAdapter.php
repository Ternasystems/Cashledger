<?php

declare(strict_types=1);

namespace TS_Cache\Classes;

use Exception;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use TS_Cache\Interfaces\ICacheAdapter;
use TS_Configuration\Classes\AbstractCls;
use TS_Exception\Classes\CacheException;

/**
 * A file-based implementation of the cache adapter.
 * Stores cache items as serialized PHP files.
 */
class FileCacheAdapter extends AbstractCls implements ICacheAdapter
{
    private const string FILE_EXTENSION = '.cache';

    /**
     * @throws CacheException
     */
    public function __construct(private readonly string $cacheDirectory)
    {
        if (!is_dir($cacheDirectory) && !mkdir($cacheDirectory, 0777, true))
            throw new CacheException('cache_dir_creation_failed', [':path' => $this->cacheDirectory]);
    }

    public function has(string $key): bool
    {
        $path = $this->getFilePath($key);
        if (!file_exists($path))
            return false;

        // Using @ to suppress warnings on invalid or empty files.
        $content = @file_get_contents($path);
        if ($content === false) return false;

        $data = @unserialize($content);
        if (!is_array($data) || !isset($data['expires']))
            return false;

        // Check if the item has expired
        return time() < $data['expires'];
    }

    public function get(string $key): mixed
    {
        if (!$this->has($key))
            return null;

        $content = @file_get_contents($this->getFilePath($key));
        if ($content === false) return null;

        $data = @unserialize($content);
        return is_array($data) ? $data['value'] : null;
    }

    public function set(string $key, mixed $value, int $seconds): bool
    {
        $path = $this->getFilePath($key);
        $data = [
            'expires' => time() + $seconds,
            'value' => $value,
        ];
        $content = serialize($data);

        return file_put_contents($path, $content, LOCK_EX) !== false;
    }

    public function delete(string $key): bool
    {
        $path = $this->getFilePath($key);
        if (file_exists($path))
            return unlink($path);

        return true;
    }

    public function flush(): bool
    {
        try
        {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($this->cacheDirectory, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($files as $fileinfo) {
                if ($fileinfo->isDir())
                    rmdir($fileinfo->getRealPath());
                else
                    unlink($fileinfo->getRealPath());
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Generates a safe and structured file path from a cache key.
     */
    private function getFilePath(string $key): string
    {
        // Use a strong hash to create a safe filename from the key.
        $hash = hash('sha256', $key);
        // Create subdirectories to avoid putting too many files in one folder.
        return $this->cacheDirectory . DIRECTORY_SEPARATOR . substr($hash, 0, 2) . DIRECTORY_SEPARATOR . substr($hash, 2, 2) . DIRECTORY_SEPARATOR .
            $hash . self::FILE_EXTENSION;
    }
}