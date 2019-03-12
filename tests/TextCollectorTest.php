<?php

namespace SilverStripe\TextCollector\Tests;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\TextCollector\CollectorInterface;
use SilverStripe\TextCollector\TextCollector;

class TextCollectorTest extends SapphireTest
{
    public function testBasicDependencyInjection()
    {
        $collector = TextCollector::singleton();
        $this->assertInstanceOf(CollectorInterface::class, $collector->getCodeCollector());
        $this->assertInstanceOf(CollectorInterface::class, $collector->getTemplateCollector());
    }
}
