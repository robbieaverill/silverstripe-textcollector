<?php declare(strict_types=1);

namespace SilverStripe\TextCollector\NodeHandler;

use PhpParser\Node\Expr;
use SilverStripe\TextCollector\Exception\UncollectableNodeException;
use SilverStripe\TextCollector\NodeHandlerInterface;
use SilverStripe\TextCollector\TextRepository;

/**
 * Variables cannot be collected, since classes are loaded at parse time rather than runtime. Ignore them.
 */
class VariableHandler implements NodeHandlerInterface
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
        return $keyNode instanceof Expr\Variable
            || $valueNode instanceof Expr\Variable;
    }

    /**
     * {@inheritDoc}
     * @throws UncollectableNodeException
     */
    public function handle(Expr $keyNode, Expr $valueNode, array $context): void
    {
        $node = $keyNode instanceof Expr\Variable ? $keyNode : $valueNode;
        $exception = new UncollectableNodeException('Incompatible node type: ' . $node->getType());
        $exception->setNode($node);
        throw $exception;
    }
}
