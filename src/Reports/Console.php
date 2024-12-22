<?php

declare(strict_types=1);

namespace Vette\Neos\CodeStyle\Reports;

use Vette\Neos\CodeStyle\Files\Error;
use Vette\Neos\CodeStyle\Files\File;

/**
 * Class Console
 *
 * @package Vette\Neos\CodeStyle\Reports
 */
class Console extends Report
{
    /**
     * @var string[]
     */
    protected array $fileReports = [];


    /**
     * @param File $file
     *
     * @return void
     */
    public function reportFile(File $file): void
    {
        if (empty($file->getErrors())) {
            return;
        }

        $path = $file->getPath();
        if (strlen($path) > 111) {
            $offset = strlen($path) - 108;
            $path = '...' . substr($path, $offset);
        }

        $path = str_pad($path, 111);

        $lines = str_repeat('-', 118) . PHP_EOL;
        $lines .= ' File ' . $path . PHP_EOL;
        $lines .= str_repeat('=', 118) . PHP_EOL;

        foreach ($file->getErrors() as $error) {
            $severity = str_pad($this->formatErrorSeverity($error->getSeverity()), 5);
            $lines .= $this->colorLog(str_pad((string)$error->getLineNumber(), 4, ' ', STR_PAD_LEFT) . ' | ' . $severity . ' | ' . str_pad($error->getMessage(), 103), $error->getSeverity()) . PHP_EOL;
        }

        $this->fileReports[] = $lines;
    }

    /**
     * @param string $severity
     * @return string
     */
    protected function formatErrorSeverity(string $severity): string
    {
        return match ($severity) {
            Error::SEVERITY_WARNING => 'WARN',
            Error::SEVERITY_ERROR => 'ERROR',
            default => 'INFO',
        };
    }

    protected function colorLog(string $str, string $type = Error::SEVERITY_INFO): string
    {
        return match ($type) {
            Error::SEVERITY_ERROR => "\033[31m$str \033[0m",
            Error::SEVERITY_WARNING => "\033[33m$str \033[0m",
            Error::SEVERITY_INFO => "\033[36m$str \033[0m",
            default => $str,
        };

    }

    /**
     * @return string
     */
    public function generate(): string
    {
        return implode(PHP_EOL, $this->fileReports);
    }
}
