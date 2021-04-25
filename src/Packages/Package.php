<?php

declare(strict_types=1);

namespace Vette\Neos\CodeStyle\Packages;

/**
 * Class Package
 *
 * @package Vette\Neos\CodeStyle\Packages
 */
class Package
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $realPath;


    /**
     * Package constructor.
     *
     * @param string $key
     * @param string $path
     * @param string $realPath
     */
    public function __construct(string $key, string $path, string $realPath)
    {
        $this->key = $key;
        $this->path = $path;
        $this->realPath = $realPath;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getRealPath(): string
    {
        return $this->realPath;
    }
}
