<?php declare(strict_types=1);

namespace SilverStripe\TextCollector\NodeHandler;

use PhpParser\NameContext;
use PhpParser\Node\Expr;
use SilverStripe\TextCollector\NodeHandlerInterface;
use SilverStripe\TextCollector\TextRepository;

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

    public function handle(Expr $keyNode, Expr $valueNode)
    {
        /** @var Expr\BinaryOp\Concat $keyNode */
        $left = $keyNode->left;
        $className = implode('\\', $this->nameContext->getResolvedClassName($left->class)->parts);
        $this->repository->add($className . $keyNode->right->value, $valueNode->value);
    }
}
