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

        $this->assertSame(
            'Explicit FQCN title',
            $result['SilverStripe\\TextCollector\\Tests\\BasicClass.TITLE']
        );

        $this->assertSame(
            'SiteTree::class title',
            $result[SiteTree::class . '.TITLE']
        );

        $this->assertSame(
            '__CLASS__ title',
            $result['SilverStripe\\TextCollector\\Tests\\BasicClass.CLASS_TITLE']
        );

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

        $this->assertSame(
            'Field A',
            $result['SilverStripe\\TextCollector\\Tests\\NestedFormFields.FieldA']
        );

        $this->assertSame(
            'Dropdown Field',
            $result['SilverStripe\\TextCollector\\Tests\\NestedFormFields.DropdownField']
        );

        $this->assertSame(
            'Checkbox Field',
            $result['NestedFormFields.CheckboxField']
        );
    }

    public function testGetPlaceholders()
    {
        $fixture = FixtureLoader::load('Placeholders');
        $collector = new CodeCollector();
        $result = $collector->collect($fixture);

        $this->assertSame(
            'Get {amount} items for lunch',
            $result['SilverStripe\\TextCollector\\Tests\\Placeholders.LUNCH_AMOUNT']
        );
    }

    public function testConcatenationInEntityValues()
    {
        $fixture = FixtureLoader::load('ConcatenationInEntityValues');
        $collector = new CodeCollector();
        $result = $collector->collect($fixture);

        $this->assertSame('Line 1 and Line \'2\' and Line "3"', $result['Test.CONCATENATED']);
        $this->assertSame('Line 1 Line 2 Line 3 Line 4 Line 5 Line 6', $result['Test.MOARCONCATENATED']);
        $this->assertSame('I would like 3 lunches please', $result['Test.INTCONCAT']);
    }
}
