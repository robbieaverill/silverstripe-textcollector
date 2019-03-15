<?php

namespace SilverStripe\TextCollector\Exception;

use Exception;
use PhpParser\Node\Expr;

/**
 * The current node type has no appropriate NodeHandlerInterfaces defined to collect it
 */
class UncollectableNodeException extends Exception
{
    /**
     * @var Expr
     */
    private $node;

    /**
     * @param Expr $node
     */
    public function setNode(Expr $node)
    {
        $this->node = $node;
    }

    /**
     * @return Expr
     */
    public function getNode(): Expr
    {
        return $this->node;
    }
}
