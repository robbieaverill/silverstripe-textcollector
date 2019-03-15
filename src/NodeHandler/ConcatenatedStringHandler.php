<?php declare(strict_types=1);

namespace SilverStripe\TextCollector\NodeHandler;

use PhpParser\NameContext;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\MagicConst;
use PhpParser\Node\Scalar\String_;
use SilverStripe\TextCollector\Exception\UncollectableNodeException;
use SilverStripe\TextCollector\NodeHandlerInterface;
use SilverStripe\TextCollector\TextRepository;

/**
 * Handles values like `'Foo' . ' bar ' . baz'`
 */
class ConcatenatedStringHandler implements NodeHandlerInterface
{
    /**
     * @var TextRepository
     */
    protected $repository;

    /**
     * @var NameContext
     */
    protected $nameContext;

    /**
     * @param TextRepository $repository
     */
    public function __construct(TextRepository $repository, NameContext $nameContext)
    {
        $this->repository = $repository;
        $this->nameContext = $nameContext;
    }

    public function canHandle(Expr $keyNode, Expr $valueNode): bool
    {
        return $keyNode instanceof Expr\BinaryOp\Concat
            || $valueNode instanceof Expr\BinaryOp\Concat;
    }

    public function handle(Expr $keyNode, Expr $valueNode, array $context): void
    {
        $this->repository->addString(
            $this->flattenConcatenatedNodes($keyNode, $context),
            $this->flattenConcatenatedNodes($valueNode, $context)
        );
    }

    /**
     * @param Expr $node
     * @param array $context
     * @return string
     * @throws UncollectableNodeException
     */
    protected function flattenConcatenatedNodes(Expr $node, array $context): string
    {
        if ($node instanceof Expr\BinaryOp\Concat) {
            $left = $this->flattenConcatenatedNodes($node->left, $context);
            $right = $this->flattenConcatenatedNodes($node->right, $context);
            return $left . $right;
        }

        if ($node instanceof String_ || $node instanceof LNumber) {
            return (string) $node->value;
        }

        if ($node instanceof MagicConst) {
            return $context['currentClass'];
        }

        if ($node instanceof Expr\ClassConstFetch) {
            if (in_array($node->class->parts, [['self'], ['static']])) {
                // Handle self::class constants (and treat static::class as self::class, because it's not collectable)
                return $context['currentClass'];
            }
            // Handle SomeClass::class constants
            return implode('\\', $this->nameContext->getResolvedClassName($node->class)->parts);
        }

        $exception = new UncollectableNodeException();
        $exception->setNode($node);
        throw $exception;
    }
}
