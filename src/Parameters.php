<?php

declare(strict_types=1);

namespace Vette\Neos\CodeStyle;

/**
 * Class Config
 *
 * @package Vette\Neos\CodeStyle
 */
class Parameters
{

    /**
     * @var string[]
     */
    protected array $files = [];

    /**
     * @var string[]
     */
    protected array $includes = [];

    protected ?string $neosRoot;

    protected ?string $configFile;

    protected ?string $ruleset;

    protected ?string $report;


    /**
     * @return string|null
     */
    public function getNeosRoot(): ?string
    {
        return $this->neosRoot;
    }

    /**
     * @return string[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @return string[]
     */
    public function getIncludes(): array
    {
        return $this->includes;
    }

    /**
     * @param ?string $neosRoot
     */
    public function setNeosRoot(?string $neosRoot): void
    {
        $this->neosRoot = $neosRoot;
    }

    /**
     * @param string[] $files
     */
    public function setFiles(array $files): void
    {
        $this->files = $files;
    }

    /**
     * @return string|null
     */
    public function getConfigFile(): ?string
    {
        return $this->configFile;
    }

    /**
     * @param string|null $configFile
     */
    public function setConfigFile(?string $configFile): void
    {
        $this->configFile = $configFile;
    }

    /**
     * @return string|null
     */
    public function getRuleset(): ?string
    {
        return $this->ruleset;
    }

    /**
     * @param string|null $ruleset
     */
    public function setRuleset(?string $ruleset): void
    {
        $this->ruleset = $ruleset;
    }

    /**
     * @return string|null
     */
    public function getReport(): ?string
    {
        return $this->report;
    }

    /**
     * @param string|null $report
     */
    public function setReport(?string $report): void
    {
        $this->report = $report;
    }
}
