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
    abstract public function reportFile(File $file): void;
    abstract public function generate(): string;
}
