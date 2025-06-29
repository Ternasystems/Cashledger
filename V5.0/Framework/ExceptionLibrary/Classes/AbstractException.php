<?php

declare(strict_types=1);

namespace TS_Exception\Classes;

use Exception;
use Throwable;

/**
 * Base class for all custom exceptions in the framework.
 *
 * Provides functionality for handling localized exception messages.
 */
abstract class AbstractException extends Exception
{
    /**
     * @param array<string, string> $localizedMessages An associative array of language codes to messages.
     * @param int $code The Exception code.
     * @param ?Throwable $previous The previous throwable used for exception chaining.
     */
    public function __construct(
        protected readonly array $localizedMessages = [],
        int $code = 0,
        ?Throwable $previous = null
    ) {
        // Find a default message from the localized messages to pass to the parent constructor.
        // It will try to find 'en' (English), otherwise it uses the first available message.
        $defaultMessage = $this->getLocalizedMessage('en', '');

        parent::__construct($defaultMessage, $code, $previous);
    }

    /**
     * Gets the exception message for a specific language.
     *
     * @param string $lang The desired language code (e.g., 'en', 'fr').
     * @param string|null $fallbackLang The language to use if the desired one isn't found.
     * If null, returns the original default message.
     * @return string The localized message.
     */
    public function getLocalizedMessage(string $lang, ?string $fallbackLang = 'en'): string
    {
        if (isset($this->localizedMessages[$lang])) {
            return $this->localizedMessages[$lang];
        }

        if ($fallbackLang && isset($this->localizedMessages[$fallbackLang])) {
            return $this->localizedMessages[$fallbackLang];
        }

        // Fall back to the default message stored in the parent Exception class.
        return $this->getMessage();
    }
}