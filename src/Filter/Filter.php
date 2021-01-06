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
 */
class Filter extends RecursiveFilterIterator
{

    /**
     * @var string[]
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
        if ($realPath !== false) {
            if (isset($this->acceptedPaths[$realPath]) === true) {
                return false;
            }
        }

        if (is_dir($filePath) === false) {
            $fileName = basename($filePath);
            $fileParts = explode('.', $fileName);
            if ($fileParts[0] === $fileName || $fileParts[0] === '') {
                return false;
            }

            if (!in_array(end($fileParts), ['fusion'])) {
                return false;
            }
        }

        $this->acceptedPaths[$realPath] = true;
        return true;
    }


    /**
     * Returns an iterator for the current entry.
     *
     * @return RecursiveIterator
     */
    public function getChildren(): RecursiveIterator
    {
        $filterClass = get_called_class();
        return new $filterClass(
            new RecursiveDirectoryIterator((string)$this->current(), (RecursiveDirectoryIterator::SKIP_DOTS | FilesystemIterator::FOLLOW_SYMLINKS))
        );
    }
}