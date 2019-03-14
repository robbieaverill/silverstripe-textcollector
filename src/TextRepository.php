<?php declare(strict_types=1);

namespace SilverStripe\TextCollector;

/**
 * Holds collected text entities in a key value store. The value can be either a string, or an array of strings, e.g.:
 *
 * <code>
 * ['Foo' => 'Bar'],
 * ['Foo' => ['default' => 'Bar', 'comment' => 'Comment explaining Bar'],
 * </code>
 */
class TextRepository
{
    protected $text = [];

    public function addString(string $key, string $value): self
    {
        $this->text[$key] = $value;
        return $this;
    }

    public function addArray(string $key, array $value): self
    {
        $this->text[$key] = $value;
        return $this;
    }

    public function getAll(): array
    {
        return $this->text;
    }

    public function get(string $key)
    {
        if ($this->has($key)) {
            return $this->text[$key];
        }
        return null;
    }

    public function has(string $key): bool
    {
        return isset($this->text[$key]);
    }
}
