<?php declare(strict_types=1);

namespace SilverStripe\TextCollector\Tests;

use InvalidArgumentException;

class FixtureLoader
{
    /**
     * Loads a fixtured PHP class or SilverStripe template files from the fixtures directory and returns its contents
     *
     * @param string $className
     * @param string $extension
     * @return string
     * @throws InvalidArgumentException
     */
    public static function load(string $className, string $extension = 'php'): string
    {
        $filename = __DIR__ . '/fixtures/' . $className . '.' . $extension . '.tmpl';
        if (!file_exists($filename)) {
            throw new InvalidArgumentException(
                'Fixture file ' . $className . ' with extension ' . $extension . ' not found.'
            );
        }
        return (string) file_get_contents($filename);
    }
}
