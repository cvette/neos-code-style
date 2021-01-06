<?php

declare(strict_types=1);

namespace Vette\Neos\CodeStyle\Reports;

use Vette\Neos\CodeStyle\Files\File;

/**
 * Class Report
 *
 * @package Vette\Neos\CodeStyle\Reports
 */
abstract class Report
{
    public abstract function reportFile(File $file): void;
    public abstract function generate(): string;
}