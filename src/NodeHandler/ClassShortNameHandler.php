<?php declare(strict_types=1);

namespace SilverStripe\TextCollector\NodeHandler;

use PhpParser\NameContext;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;
use SilverStripe\TextCollector\NodeHandlerInterface;
use SilverStripe\TextCollector\TextRepository;

/**
 * Handles keys like `SomeClass::class . '.MY_TRANSLATION'`
 */
class ClassShortNameHandler implements NodeHandlerInterface
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
            && $keyNode->left instanceof Expr\ClassConstFetch;
    }

    public function handle(Expr $keyNode, Expr $valueNode, array $context)
    {
        /** @var Expr\BinaryOp\Concat $keyNode */
        /** @var Expr\ClassConstFetch $left */
        /** @var Scalar\String_ $valueNode */
        $left = $keyNode->left;
        if ($left->class->parts === ['self']) {
            // Handle self::class constants
            $className = $context['currentClass'];
        } else {
            // Handle SomeClass::class constants
            $className = implode('\\', $this->nameContext->getResolvedClassName($left->class)->parts);
        }
        $this->repository->addString($className . $keyNode->right->value, $valueNode->value);
    }
}
