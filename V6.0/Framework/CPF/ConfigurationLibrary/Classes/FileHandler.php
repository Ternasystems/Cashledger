<?php

declare(strict_types=1);

namespace TS_Configuration\Classes;

use DateTime;
use DateTimeInterface;
use DOMException;
use Exception;
use TS_Configuration\Interfaces\IConfigurationManager;
use TS_Configuration\Interfaces\ILogHandler;
use TS_Exception\Classes\JsonException;
use TS_Exception\Classes\XMLException;

class FileHandler extends AbstractCls implements ILogHandler
{
    private string $logDirectory;
    private string $format;

    public function __construct(string $logDirectory, string $format = 'json')
    {
        if (!is_dir($logDirectory)) {
            mkdir($logDirectory, 0777, true);
        }
        $this->logDirectory = rtrim($logDirectory, DIRECTORY_SEPARATOR);
        $this->format = strtolower($format);
    }

    public function log(string $level, string $message, array $context): void
    {
        try {
            $manager = $this->getManagerForToday();

            $logEntry = [
                '@timestamp' => new DateTime()->format(DateTimeInterface::ATOM),
                '@level' => $level,
                'message' => $message,
                'context' => !empty($context) ? json_encode($context) : null,
            ];

            if ($this->format === 'xml') {
                $logs = $manager->get('log', []);
                // Ensure logs is an array
                $logs = is_array($logs) ? $logs : [$logs];
                if (empty($logs[0])) unset($logs[0]); // Remove empty item if it exists
                $logs[] = $logEntry;
                $manager->set('log', $logs);
            } else {
                $logs = $manager->get('logs', []);
                $logs[] = $logEntry;
                $manager->set('logs', $logs);
            }

            $manager->save();
        } catch (Exception $e) {
            // Failsafe: If logging fails, write to the PHP error log to avoid crashing the app.
            error_log("Logging failed: " . $e->getMessage());
        }
    }

    /**
     * @throws DOMException
     * @throws JsonException
     * @throws XMLException
     */
    private function getManagerForToday(): IConfigurationManager
    {
        $filename = 'cashledger-' . date('Y-m-d') . '.' . $this->format;
        $filepath = $this->logDirectory . DIRECTORY_SEPARATOR . $filename;

        if ($this->format === 'xml') {
            return new XMLManager($filepath, 'logs');
        }

        return new JsonManager($filepath, ['logs' => []]);
    }
}
