<?php

declare(strict_types=1);

namespace TS_Exception\Classes;

use Exception;
use ReflectionClass;
use Throwable;
use TS_Locale\Classes\Translator;

/**
 * The modernized base class for all custom exceptions in the framework.
 * It uses a centralized Translator service for localized error messages.
 */
abstract class AbstractException extends Exception
{
    /** The globally available translator instance. */
    private static ?Translator $translator = null;

    /**
     * @param string $messageKey The key for the translation lookup (e.g., 'database.connection_failed').
     * @param array<string, string|int> $placeholders Values to substitute into the message.
     * @param int $code The Exception code.
     * @param ?Throwable $previous The previous throwable used for exception chaining.
     */
    public function __construct(
        string $messageKey = '',
        protected readonly array $placeholders = [],
        int $code = 0,
        ?Throwable $previous = null
    ) {
        // The message passed to the parent is the raw message key.
        // The original getMessage() will now return this key.
        parent::__construct($messageKey, $code, $previous);
    }

    /**
     * Injects the Translator service for all exceptions to use.
     * This should be called once during application bootstrap.
     */
    public static function setTranslator(Translator $translator): void
    {
        self::$translator = $translator;
    }

    /**
     * Gets the translated, user-friendly error message.
     *
     * This method contains the logic to look up the message key,
     * apply placeholders, and return the final string.
     *
     * @return string The final, translated, and formatted error message.
     */
    public function getTranslatedMessage(): string
    {
        $messageKey = parent::getMessage();

        // If the translator hasn't been set, return a helpful debug message.
        if (self::$translator === null) {
            $rawMessage = 'Translator not set. Key: "' . $messageKey . '"';
            if (!empty($this->placeholders)) {
                $rawMessage .= ' | Placeholders: ' . json_encode($this->placeholders);
            }
            return $rawMessage;
        }

        // Dynamically create the full translation key
        // e.g., if this is a DBException, the domain becomes "DBException"
        $domain = new ReflectionClass($this)->getShortName();
        $fullKey = "{$domain}.{$messageKey}";

        return self::$translator->trans($fullKey, $this->placeholders);
    }
}
