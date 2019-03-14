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

        $this->assertArrayHasKey('SilverStripe\\TextCollector\\Tests\\BasicClass.CLASS_TITLE', $result);
        $this->assertSame(
            '__CLASS__ title',
            $result['SilverStripe\\TextCollector\\Tests\\BasicClass.CLASS_TITLE']
        );

        $this->assertArrayHasKey('SilverStripe\\TextCollector\\Tests\\BasicClass.SELF_TITLE', $result);
        $this->assertSame(
            'self::class title',
            $result['SilverStripe\\TextCollector\\Tests\\BasicClass.SELF_TITLE']
        );
    }

    public function testNestedFormFieldCollection()
    {
        $fixture = FixtureLoader::load('NestedFormFields');
        $collector = new CodeCollector();
        $result = $collector->collect($fixture);

        $this->assertArrayHasKey('SilverStripe\\TextCollector\\Tests\\NestedFormFields.FieldA', $result);
        $this->assertSame(
            'Field A',
            $result['SilverStripe\\TextCollector\\Tests\\NestedFormFields.FieldA']
        );

        $this->assertArrayHasKey('SilverStripe\\TextCollector\\Tests\\NestedFormFields.DropdownField', $result);
        $this->assertSame(
            'Dropdown Field',
            $result['SilverStripe\\TextCollector\\Tests\\NestedFormFields.DropdownField']
        );

        $this->assertArrayHasKey('NestedFormFields.CheckboxField', $result);
        $this->assertSame(
            'Checkbox Field',
            $result['NestedFormFields.CheckboxField']
        );
    }
}
