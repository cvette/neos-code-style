<?php

declare(strict_types=1);

namespace Vette\Neos\CodeStyle\Packages;

use Iterator;

/**
 * Class PackageCollection
 *
 * @package Vette\Neos\CodeStyle\Packages
 * @template-implements Iterator<Package>
 */
class PackageCollection implements Iterator
{

    /**
     * @var Package[]
     */
    protected array $packages = [];


    /**
     * PackageCollection constructor.
     *
     * @param string|null $neosRoot
     */
    public function __construct(?string $neosRoot)
    {
        if ($neosRoot !== null && is_dir($neosRoot)) {
            $path = implode(DIRECTORY_SEPARATOR, [$neosRoot, 'Data', 'Temporary', 'PackageInformationCache.php']);
            $packagesPath = implode(DIRECTORY_SEPARATOR, [$neosRoot, 'Packages']);
            if (is_file($path)) {
                $packageCache = include $path;

                /** @var array $package */
                foreach ($packageCache['packages'] as $package) {
                    $packagePath = implode(DIRECTORY_SEPARATOR, [$packagesPath, $package['packagePath']]);
                    $realPackagePath = realpath($packagePath);
                    $this->packages[$realPackagePath] = new Package($package['packageKey'], $package['packagePath'], $realPackagePath);
                }
            }
        }
    }

    /**
     * Get all packages
     *
     * @return Package[]
     */
    public function getPackages(): array
    {
        return $this->packages;
    }

    /**
     * @param string $path
     * @return Package|null
     */
    public function findPackageForRealPath(string $path): ?Package
    {
        foreach ($this->packages as $package) {
            if (str_starts_with($path, $package->getRealPath())) {
                return $package;
            }
        }

        return null;
    }

    public function current(): Package
    {
        $path = key($this->packages);
        return $this->packages[$path];
    }

    public function next(): void
    {
        next($this->packages);
    }

    public function valid(): bool
    {
        return !(current($this->packages) === false);
    }

    public function rewind(): void
    {
        reset($this->packages);
    }

    public function key(): int|string|null
    {
        return key($this->packages);
    }
}
