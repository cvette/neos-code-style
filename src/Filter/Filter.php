<?php

declare(strict_types=1);

namespace Vette\Neos\CodeStyle\Filter;

use RecursiveIterator;
use RecursiveDirectoryIterator;
use RecursiveFilterIterator;
use FilesystemIterator;

/**
 * Class Filter
 *
 * @package Vette\Neos\CodeStyle\Filter
 * @psalm-suppress MissingTemplateParam
 */
class Filter extends RecursiveFilterIterator
{

    /**
     * @var array<string,bool>
     */
    protected array $acceptedPaths = [];


    /**
     * Filter constructor.
     *
     * @param RecursiveIterator $iterator
     */
    final public function __construct(RecursiveIterator $iterator)
    {
        parent::__construct($iterator);
    }

    /**
     * @return bool
     */
    public function accept(): bool
    {
        $filePath = (string)$this->current();
        $realPath = realpath($filePath);

        if ($realPath === false) {
            return false;
        }

        if (isset($this->acceptedPaths[$realPath]) === true) {
            return false;
        }

        if (is_dir($filePath) === false) {
            $fileName = basename($filePath);
            $fileParts = explode('.', $fileName);
            if ($fileParts[0] === $fileName || $fileParts[0] === '') {
                return false;
            }

            if (end($fileParts) !== 'fusion') {
                return false;
            }
        }

        $this->acceptedPaths[$realPath] = true;
        return true;
    }


    /**
     * Returns an iterator for the current entry.
     */
    public function getChildren(): ?RecursiveFilterIterator
    {
        $filterClass = static::class;
        return new $filterClass(
            new RecursiveDirectoryIterator((string)$this->current(), (FilesystemIterator::SKIP_DOTS | FilesystemIterator::FOLLOW_SYMLINKS))
        );
    }
}
