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
 * @template-implements Iterator<File>
 */
class FileCollection implements Iterator, Countable
{
    protected PackageCollection $packageCollection;

    /**
     * @var array<File>
     */
    protected array $files = [];


    /**
     * FileCollection constructor.
     *
     * @param array<string> $filePaths
     * @param PackageCollection $packageCollection
     */
    public function __construct(array $filePaths, PackageCollection $packageCollection)
    {
        $this->packageCollection = $packageCollection;

        foreach ($filePaths as $path) {
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
            $di = new RecursiveDirectoryIterator($path, (FilesystemIterator::SKIP_DOTS | FilesystemIterator::FOLLOW_SYMLINKS));
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

    public function current(): File
    {
        $path = key($this->files);
        return $this->files[$path];
    }

    public function next(): void
    {
        next($this->files);
    }

    public function key(): int|string|null
    {
        return key($this->files);
    }

    public function valid(): bool
    {
        return !(current($this->files) === false);
    }

    public function rewind(): void
    {
        reset($this->files);
    }

    public function count(): int
    {
        return count($this->files);
    }
}
