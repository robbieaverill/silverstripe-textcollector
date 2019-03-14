<?php declare(strict_types=1);

namespace SilverStripe\TextCollector;

/**
 * Holds collected text entities in a key value store
 */
class TextRepository
{
    protected $text = [];

    public function add(string $key, ?string $value): self
    {
        $this->text[$key] = $value;
        return $this;
    }

    public function get(): array
    {
        return $this->text;
    }
}
