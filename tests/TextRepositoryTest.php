<?php declare(strict_types=1);

namespace SilverStripe\TextCollector\Tests;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\TextCollector\TextRepository;

class TextRepositoryTest extends SapphireTest
{
    public function testGet()
    {
        $repository = new TextRepository();
        $repository->addString('foo', 'bar');
        $repository->addString('bar', 'baz');

        $this->assertSame('bar', $repository->get('foo'));
        $this->assertSame('baz', $repository->get('bar'));
    }

    public function testHas()
    {
        $repository = new TextRepository();
        $repository->addArray('foo', ['bar', 'baz']);

        $this->assertTrue($repository->has('foo'));
        $this->assertFalse($repository->has('bar'));
        $this->assertSame(['bar', 'baz'], $repository->get('foo'));
    }

    public function testGetAll()
    {
        $repository = new TextRepository();
        $repository->addString('foo', 'bar');
        $repository->addArray('bar', ['baz' => 'foo']);

        $this->assertSame([
            'foo' => 'bar',
            'bar' => ['baz' => 'foo'],
        ], $repository->getAll());
    }
}
