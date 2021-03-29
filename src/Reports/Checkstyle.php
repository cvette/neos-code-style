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
class Checkstyle extends Report
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

        $output = '<file name="' . htmlspecialchars($file->getPath()) . '">' . "\n";
        foreach ($file->getErrors() as $error) {
            $output .= ' ';
            $output .= '<error';
            $output .= ' line="' . $error->getLineNumber() . '"';
            $output .= ' column="' . $error->getColumn() . '"';
            $output .= ' severity="' . $this->formatErrorSeverity($error->getSeverity()) . '"';
            $output .= ' message="' . htmlspecialchars($error->getMessage()) . '"';
            $output .= '/>' . "\n";
        }

        $output .= '</file>' . "\n";

        $this->fileReports[] = $output;
    }

    /**
     * @param string $severity
     * @return string
     */
    protected function formatErrorSeverity(string $severity): string
    {
        switch ($severity) {
            case Error::SEVERITY_WARNING: return 'WARN';
            case Error::SEVERITY_ERROR: return 'ERROR';
            default: return 'INFO';
        }
    }

    /**
     * @return string
     */
    public function generate(): string
    {
        $output = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $output .= '<checkstyle>' . "\n";
        $output .= join("\n", $this->fileReports);
        $output .= '</checkstyle>' . "\n";
        return $output;
    }
}