<?php declare(strict_types=1);

namespace SilverStripe\TextCollector\NodeHandler;

use PhpParser\NameContext;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar\MagicConst;
use PhpParser\Node\Scalar\String_;
use SilverStripe\TextCollector\NodeHandlerInterface;
use SilverStripe\TextCollector\TextRepository;

/**
 * Handles keys with magic class constant: `__CLASS__ . '.MY_TRANSLATION'`
 */
class MagicClassConstantHandler implements NodeHandlerInterface
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
            && $keyNode->left instanceof MagicConst\Class_
            && $valueNode instanceof String_;
    }

    public function handle(Expr $keyNode, Expr $valueNode, array $context)
    {
        /** @var Expr\BinaryOp\Concat $keyNode */
        /** @var String_ $valueNode */
        $this->repository->addString($context['currentClass'] . $keyNode->right->value, $valueNode->value);
    }
}
