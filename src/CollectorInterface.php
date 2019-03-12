<?php declare(strict_types=1);

namespace SilverStripe\TextCollector;

/**
 * A CollectorInterface will be responsible for collecting text from the provided content and
 * will return the results of those found.
 */
interface CollectorInterface
{
    /**
     * The i18n function name to collect
     *
     * @var string
     */
    const FUNCTION_NAME = '_t';

    /**
     * Collects translations from the given content, and returns an array of those found
     *
     * @param string $content
     * @return string[]
     */
    public function collect(string $content): array;
}
