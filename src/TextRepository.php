<?php declare(strict_types=1);

namespace SilverStripe\TextCollector;

class TextRepository
{
    protected $text = [];

    public function add(string $key, string $value): self
    {
        $this->text[$key] = $value;
        return $this;
    }

    public function get(): array
    {
        return $this->text;
    }
}
