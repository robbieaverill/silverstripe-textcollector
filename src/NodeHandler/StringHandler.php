<?php declare(strict_types=1);

namespace SilverStripe\TextCollector\NodeHandler;

use PhpParser\Node\Expr;
use PhpParser\Node\Scalar\String_;
use SilverStripe\TextCollector\NodeHandlerInterface;
use SilverStripe\TextCollector\TextRepository;

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
        return $keyNode instanceof String_;
    }

    public function handle(Expr $keyNode, Expr $valueNode)
    {
        /** @var String_ $keyNode */
        /** @var String_ $valueNode */
        $this->repository->add($keyNode->value, $valueNode->value);
    }
}