<?php

declare(strict_types=1);

namespace Vette\Neos\CodeStyle\Files;

use Iterator;
use Countable;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Vette\Neos\CodeStyle\Filter\Filter;
use Vette\Neos\CodeStyle\Packages\PackageCollection;

/**
 * Class FileCollection
 *
 * @package Vette\Neos\CodeStyle\Files
 */
class FileCollection implements Iterator, Countable
{
    /**
     * @var PackageCollection
     */
    protected PackageCollection $packageCollection;

    /**
     * @var File[]
     */
    protected array $files = [];


    /**
     * FileCollection constructor.
     *
     * @param array $files
     * @param PackageCollection $packageCollection
     */
    public function __construct(array $files, PackageCollection $packageCollection)
    {
        $this->packageCollection = $packageCollection;

        foreach ($files as $path) {
            $this->addPath($path);
        }

        reset($this->files);
    }

    /**
     * Adds a path to this file collection
     *
     * @param string $path
     *
     * @return void
     */
    protected function addPath(string $path): void
    {
        if (is_dir($path)) {
            $di = new RecursiveDirectoryIterator($path, (RecursiveDirectoryIterator::SKIP_DOTS | FilesystemIterator::FOLLOW_SYMLINKS));
            $filter   = new Filter($di);
            $iterator = new RecursiveIteratorIterator($filter);

            foreach ($iterator as $file) {
                $path = $file->getPathname();
                $this->addFile($path);
            }
        } else {
            $this->addFile($path);
        }
    }

    /**
     * Adds a file to this collection
     *
     * @param string $path
     *
     * @return void
     */
    protected function addFile(string $path): void
    {
        $realPath = realpath($path);
        if ($realPath !== false && !isset($this->files[$realPath])) {
            $this->files[$realPath] = new File($path, $this->packageCollection->findPackageForRealPath($realPath));
        }
    }

    public function current()
    {
        $path = key($this->files);
        return $this->files[$path];
    }

    public function next()
    {
        next($this->files);
    }

    public function key()
    {
        return key($this->files);
    }

    public function valid()
    {
        if (current($this->files) === false) {
            return false;
        }

        return true;
    }

    public function rewind()
    {
        reset($this->files);
    }

    public function count()
    {
        return count($this->files);
    }
}
