<?php declare(strict_types=1);

namespace SilverStripe\TextCollector\Tests\Collector;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\TextCollector\Collector\CodeCollector;
use SilverStripe\TextCollector\Tests\FixtureLoader;

class CodeCollectorTest extends SapphireTest
{
    public function testBasicClassCollection()
    {
        $fixture = FixtureLoader::load('BasicClass');
        $collector = new CodeCollector();
        $result = $collector->collect($fixture);

        $this->assertArrayHasKey('SilverStripe\\TextCollector\\Tests\\BasicClass.TITLE', $result);
        $this->assertSame(
            'Explicit FQCN title',
            $result['SilverStripe\\TextCollector\\Tests\\BasicClass.TITLE']
        );
        $this->assertArrayHasKey(SiteTree::class . '.TITLE', $result);
        $this->assertSame(
            'SiteTree::class title',
            $result[SiteTree::class . '.TITLE']
        );
    }
}
