<?php

declare(strict_types=1);

namespace TS_Configuration\Classes;

use DateTime;
use DateTimeInterface;
use Exception;
use TS_Configuration\Interfaces\ILogHandler;
use TS_Exception\Classes\StreamException;

/**
 * A high-performance log handler that writes to a plain text file stream.
 */
class StreamFileHandler extends AbstractCls implements ILogHandler
{
    private TextStreamManager $manager;

    /**
     * @throws StreamException
     */
    public function __construct(string $logDirectory, string $filename)
    {
        if (!is_dir($logDirectory)) {
            mkdir($logDirectory, 0777, true);
        }
        $logPath = rtrim($logDirectory, DIRECTORY_SEPARATOR);
        $filename .= '.log';
        $filepath = $logPath . DIRECTORY_SEPARATOR . $filename;

        // Using a tab delimiter for a simple, structured text format.
        $this->manager = new TextStreamManager($filepath, "\t");
    }

    public function log(string $level, string $message, array $context): void
    {
        try {
            $timestamp = new DateTime()->format(DateTimeInterface::ATOM);
            $contextString = !empty($context) ? json_encode($context) : '{}';

            $data = [
                $timestamp,
                $level,
                $message,
                $contextString
            ];

            $this->manager->append($data);
        } catch (Exception $e) {
            // Failsafe: If logging fails, write to the PHP error log.
            error_log("Stream logging failed: " . $e->getMessage());
        }
    }
}
