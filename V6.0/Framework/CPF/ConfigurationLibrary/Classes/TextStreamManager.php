<?php

declare(strict_types=1);

namespace TS_Configuration\Classes;

use TS_Exception\Classes\StreamException;

/**
 * Manages simple, append-only text files (streams).
 * Optimized for high-performance writing, ideal for logging.
 * This class does NOT implement IConfigurationManager as its purpose is different.
 */
class TextStreamManager extends AbstractCls
{
    protected readonly string $file;
    private string $delimiter;

    /**
     * @param string $filepath Path to the text file.
     * @param string $delimiter The character(s) to place between data fields. Use PHP_EOL for line-by-line logging.
     * @throws StreamException
     */
    public function __construct(string $filepath, string $delimiter = PHP_EOL)
    {
        $this->file = $filepath;
        $this->delimiter = $delimiter;

        if (!file_exists($this->file)) {
            // Touch the file to create it.
            if (file_put_contents($this->file, '') === false) {
                throw new StreamException('file_creation_failed', [':path' => $this->file]);
            }
        }
    }

    /**
     * Appends a line of data to the file.
     *
     * @param array<scalar> $data The data to write. It will be imploded by the delimiter.
     * @return bool True on success, false on failure.
     */
    public function append(array $data): bool
    {
        $line = implode($this->delimiter, $data) . PHP_EOL;
        return file_put_contents($this->file, $line, FILE_APPEND | LOCK_EX) !== false;
    }

    /**
     * Reads the last N lines from the file efficiently without loading the entire file into memory.
     *
     * @param int $lineCount The number of lines to read from the end.
     * @return array<string> An array of the last lines.
     * @throws StreamException
     */
    public function readLast(int $lineCount = 10): array
    {
        if (!is_readable($this->file)) {
            throw new StreamException('file_read_failed', [':path' => $this->file]);
        }

        $fileHandle = fopen($this->file, 'r');
        if (!$fileHandle) {
            throw new StreamException('file_open_failed', [':path' => $this->file]);
        }

        $lines = [];
        $buffer = '';
        fseek($fileHandle, 0, SEEK_END);
        $fileSize = ftell($fileHandle);

        for ($i = $fileSize - 1; $i >= 0 && count($lines) < $lineCount; $i--) {
            fseek($fileHandle, $i);
            $char = fgetc($fileHandle);
            if ($char === "\n" && $buffer !== '') {
                array_unshift($lines, rtrim($buffer));
                $buffer = '';
            } else {
                $buffer = $char . $buffer;
            }
        }

        if ($buffer !== '') {
            array_unshift($lines, $buffer);
        }

        fclose($fileHandle);
        return array_slice($lines, 0, $lineCount);
    }
}
