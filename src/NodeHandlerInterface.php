<?php declare(strict_types=1);

namespace SilverStripe\TextCollector;

use PhpParser\Node\Expr;

/**
 * Handlers will be given a key and value node, will determine whether they can handle them, and will "handle"
 * them by processing the two parser nodes and pushing them to the repository. The repository will be provided
 * to the constructor.
 */
interface NodeHandlerInterface
{
    /**
     * Should this handler handle the given key/value nodes?
     *
     * @param Expr $keyNode
     * @param Expr $valueNode
     * @return bool
     */
    public function canHandle(Expr $keyNode, Expr $valueNode): bool;

    /**
     * Handle the given key/value nodes
     *
     * @param Expr $keyNode
     * @param Expr $valueNode
     */
    public function handle(Expr $keyNode, Expr $valueNode);
}
