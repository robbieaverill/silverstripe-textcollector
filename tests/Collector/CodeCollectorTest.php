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
        $result = $this->collectFromFixture('BasicClass');

        $this->assertSame('Explicit FQCN title', $result['SilverStripe\\TextCollector\\Tests\\BasicClass.TITLE']);
        $this->assertSame('SiteTree::class title', $result[SiteTree::class . '.TITLE']);
        $this->assertSame('__CLASS__ title', $result['SilverStripe\\TextCollector\\Tests\\BasicClass.CLASS_TITLE']);
        $this->assertSame('self::class title', $result['SilverStripe\\TextCollector\\Tests\\BasicClass.SELF_TITLE']);
        $this->assertSame('Empty', $result['SilverStripe\\TextCollector\\Tests\\BasicClass.EMPTY']);
    }

    public function testNestedFormFieldCollection()
    {
        $result = $this->collectFromFixture('NestedFormFields');

        $this->assertSame('Field A', $result['SilverStripe\\TextCollector\\Tests\\NestedFormFields.FieldA']);
        $this->assertSame('Dropdown Field', $result['SilverStripe\\TextCollector\\Tests\\NestedFormFields.Drop']);
        $this->assertSame('Checkbox Field', $result['NestedFormFields.CheckboxField']);
    }

    public function testGetPlaceholders()
    {
        $result = $this->collectFromFixture('Placeholders');

        $this->assertSame(
            'Get {amount} items for lunch',
            $result['SilverStripe\\TextCollector\\Tests\\Placeholders.LUNCH_AMOUNT']
        );
    }

    public function testConcatenationInEntityValues()
    {
        $result = $this->collectFromFixture('ConcatenationInEntityValues');

        $this->assertSame('Line 1 and Line \'2\' and Line "3"', $result['Test.CONCATENATED']);
        $this->assertSame('Line 1 Line 2 Line 3 Line 4 Line 5 Line 6', $result['Test.MOARCONCATENATED']);
        $this->assertSame('I would like 3 lunches please', $result['Test.INTCONCAT']);
    }

    public function testEntityWithComment()
    {
        $result = $this->collectFromFixture('WithComments');

        $this->assertSame('An example string', $result['Test.EXAMPLE1']['default']);
        $this->assertSame('Comment describing the example string', $result['Test.EXAMPLE1']['comment']);

        $this->assertSame('An example string', $result['Test.EXAMPLE2']['default']);
        $this->assertSame('Comment', $result['Test.EXAMPLE2']['comment']);

        $this->assertSame('An example string', $result['Test.EXAMPLE3']['default']);
        $this->assertSame('Comment', $result['Test.EXAMPLE3']['comment']);
    }

    public function testPluralisation()
    {
        $result = $this->collectFromFixture('Pluralisation');

        $this->assertSame([
            'one' => 'An item',
            'other' => '{count} items',
            'comment' => 'Test Pluralisation',
        ], $result['i18nTestModule.INJECTIONS9']);
    }

    public function testTranslationInsideDBField()
    {
        $result = $this->collectFromFixture('TranslationInsideDBField');
        $this->assertSame(
            'You must log in with your CMS password in order to view the draft or archived content. '
            . '<a href="{link}">Click here to go back to the published site.</a>',
            $result['SilverStripe\\TextCollector\\Tests\\TranslationInsideDBField.DRAFT_SITE_ACCESS_RESTRICTION']
        );
    }

    /**
     * @expectedException \SilverStripe\TextCollector\Exception\MissingDefaultValueException
     * @expectedExceptionMessage Test.NO_VALUE missing default translation value
     */
    public function testTranslationsWithoutDefaultValues()
    {
        $this->collectFromFixture('MissingValues');
    }

    /**
     * Loads a fixtured PHP template file from the fixtures folder and returns the collected contents of it
     *
     * @param string $fixture
     * @return array
     */
    private function collectFromFixture(string $fixture): array
    {
        $fixture = FixtureLoader::load($fixture);
        $collector = new CodeCollector();
        return $collector->collect($fixture);
    }
}
