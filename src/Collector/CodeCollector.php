<?php declare(strict_types=1);

namespace SilverStripe\TextCollector\Collector;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use SilverStripe\TextCollector\CollectorInterface;
use SilverStripe\TextCollector\TextRepository;
use SilverStripe\TextCollector\Visitor\TextCollectorVisitor;

/**
 * Collects translations from PHP code.
 *
 * This class works off the assumption that translation calls are in the following format:
 *
 * <code>
 * _t(\Fully\Qualified\ClassName::class . '.KEY', 'Value')
 * </code>
 */
class CodeCollector implements CollectorInterface
{
    /**
     * @var Parser
     */
    protected $parser;

    public function collect(string $contents): array
    {
        $parser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
        $traverser = new NodeTraverser();

        $collectedText = new TextRepository();
        $nameResolver = new NameResolver();
        $traverser->addVisitor($nameResolver);
        $traverser->addVisitor(new TextCollectorVisitor($collectedText, $nameResolver->getNameContext()));

        $ast = $parser->parse($contents);
        $traverser->traverse($ast);

        return $collectedText->getAll();
    }
}
