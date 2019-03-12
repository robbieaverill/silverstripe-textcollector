<?php declare(strict_types=1);

namespace SilverStripe\TextCollector\Collector;

use SilverStripe\TextCollector\CollectorInterface;

class TemplateCollector implements CollectorInterface
{
    public function collect(string $contents): array
    {
        return [];
    }
}
