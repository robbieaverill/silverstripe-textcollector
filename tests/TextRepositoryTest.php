<?php declare(strict_types=1);

namespace SilverStripe\TextCollector\Tests;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\TextCollector\TextRepository;

class TextRepositoryTest extends SapphireTest
{
    public function testGet()
    {
        $repository = new TextRepository();
        $repository->add('foo', 'bar');
        $repository->add('bar', 'baz');

        $this->assertSame('bar', $repository->get()['foo']);
        $this->assertSame('baz', $repository->get()['bar']);
    }
}
