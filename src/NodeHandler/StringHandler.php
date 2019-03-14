<?php declare(strict_types=1);

namespace SilverStripe\TextCollector\NodeHandler;

use PhpParser\Node\Expr;
use PhpParser\Node\Scalar\String_;
use SilverStripe\TextCollector\NodeHandlerInterface;
use SilverStripe\TextCollector\TextRepository;

/**
 * Handles keys like `'FooBar.VALUE'` or `'SilverStripe\\Model\\FooBar.Model'`
 */
class StringHandler implements NodeHandlerInterface
{
    /**
     * @var TextRepository
     */
    protected $repository;

    /**
     * @param TextRepository $repository
     */
    public function __construct(TextRepository $repository)
    {
        $this->repository = $repository;
    }

    public function canHandle(Expr $keyNode, Expr $valueNode): bool
    {
        return $keyNode instanceof String_ && $valueNode instanceof String_;
    }

    public function handle(Expr $keyNode, Expr $valueNode, array $context)
    {
        /** @var String_ $keyNode */
        /** @var String_ $valueNode */
        $this->repository->addString($keyNode->value, $valueNode->value);
    }
}
