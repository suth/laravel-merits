<?php

namespace Suth\Merits;

use Illuminate\Support\Collection;
use ReflectionClass;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class BadgeDiscovery
{
    /**
     * @return Collection<int, class-string<Badge>>
     */
    public static function within(string $path, string $namespace): Collection
    {
        if (! is_dir($path)) {
            return collect();
        }

        return collect(Finder::create()->files()->in($path)->name('*.php'))
            ->map(fn (SplFileInfo $file) => static::classFromFile($file, $path, $namespace))
            ->filter(fn (string $class) => class_exists($class)
                && is_subclass_of($class, Badge::class)
                && ! (new ReflectionClass($class))->isAbstract())
            ->values();
    }

    protected static function classFromFile(SplFileInfo $file, string $basePath, string $namespace): string
    {
        $relative = trim(str_replace(realpath($basePath), '', $file->getRealPath()), DIRECTORY_SEPARATOR);

        return $namespace.'\\'.str_replace([DIRECTORY_SEPARATOR, '.php'], ['\\', ''], $relative);
    }
}
