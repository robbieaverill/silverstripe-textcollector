<?php declare(strict_types=1);

namespace SilverStripe\TextCollector\NodeHandler;

use PhpParser\Node\Expr;
use PhpParser\Node\Scalar\String_;
use SilverStripe\TextCollector\NodeHandlerInterface;
use SilverStripe\TextCollector\TextRepository;
use function array_reverse;

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
     * @param TextRepository $repository
     */
    public function __construct(TextRepository $repository)
    {
        $this->repository = $repository;
    }

    public function canHandle(Expr $keyNode, Expr $valueNode): bool
    {
        return $keyNode instanceof String_
            && $valueNode instanceof Expr\BinaryOp\Concat;
    }

    public function handle(Expr $keyNode, Expr $valueNode, array $context)
    {
        /** @var String_ $keyNode */
        /** @var Expr\BinaryOp\Concat $valueNode */

        // Add the right side of the original node
        $valueParts = [$valueNode->right->value];

        // Handle recursive concatenation nodes
        $currentNode = $valueNode->left;
        while ($currentNode instanceof Expr\BinaryOp\Concat) {
            if ($currentNode->left instanceof String_) {
                // Push values in reverse
                $valueParts[] = $currentNode->right->value;
                $valueParts[] = $currentNode->left->value;
                // Reached the bottom of the tree
                break;
            }
            // Push the right leaf node anyway, it's always a string
            $valueParts[] = $currentNode->right->value;
            // Update current node pointer
            $currentNode = $currentNode->left;
        }

        // Flip the array direction
        $valueParts = array_reverse($valueParts);

        $this->repository->addString($keyNode->value, implode($valueParts));
    }
}
