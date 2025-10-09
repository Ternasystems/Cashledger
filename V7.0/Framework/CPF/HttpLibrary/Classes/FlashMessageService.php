<?php

declare(strict_types=1);

namespace TS_Http\Classes;

use TS_Configuration\Classes\AbstractCls;

/**
 * Manages "flash" messages that persist for a single redirect.
 */
final class FlashMessageService extends AbstractCls
{
    private const string FLASH_KEY = 'cashledger_flash_messages';

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Sets a flash message.
     */
    public function set(string $key, mixed $value): void
    {
        $_SESSION[self::FLASH_KEY][$key] = $value;
    }

    /**
     * Gets a flash message, removing it after reading.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $message = $_SESSION[self::FLASH_KEY][$key] ?? $default;
        unset($_SESSION[self::FLASH_KEY][$key]);
        return $message;
    }

    /**
     * Checks if a flash message exists.
     */
    public function has(string $key): bool
    {
        return isset($_SESSION[self::FLASH_KEY][$key]);
    }
}
