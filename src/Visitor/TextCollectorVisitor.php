<?php declare(strict_types=1);

namespace SilverStripe\TextCollector\Visitor;

use PhpParser\NameContext;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\NodeVisitorAbstract;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\TextCollector\CollectorInterface;
use SilverStripe\TextCollector\NodeHandlerInterface;
use SilverStripe\TextCollector\TextRepository;

class TextCollectorVisitor extends NodeVisitorAbstract
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
     * @var NodeHandlerInterface[]
     */
    protected $handlers;

    /**
     * @var array
     */
    protected $context = [];

    public function __construct(TextRepository $repository, NameContext $nameContext)
    {
        $this->repository = $repository;
        $this->nameContext = $nameContext;
    }

    public function enterNode(Node $node)
    {
        // Track the current class and namespace for use in self class references
        if ($node instanceof Node\Stmt\Class_) {
            $this->context['currentClass'] = implode('\\', $node->namespacedName->parts);
        }

        // Find _t() function calls
        if (!$node instanceof FuncCall || (string) $node->name !== CollectorInterface::FUNCTION_NAME) {
            return null;
        }

        // Note: expected _t() argument structure
        list($keyNode, $valueNode) = $node->args;
        foreach ($this->getHandlers() as $handler) {
            if (!$handler->canHandle($keyNode->value, $valueNode->value)) {
                continue;
            }
            $handler->handle($keyNode->value, $valueNode->value, $this->context);
            break;
        }

        if (isset($node->args[2])) {
            $this->augmentWithComments($keyNode->value, $node->args[2]->value);
        }
    }

    /**
     * Construct and return a list of NodeHandlers
     *
     * @return NodeHandlerInterface[]
     */
    public function getHandlers(): array
    {
        if (!$this->handlers) {
            $handlerClasses = Config::inst()->get(__CLASS__, 'handlers');
            foreach ($handlerClasses as $handlerClass) {
                $this->handlers[] = Injector::inst()->create(
                    $handlerClass,
                    $this->repository,
                    $this->nameContext
                );
            }
        }
        return $this->handlers;
    }

    /**
     * If there's a third argument and it's a string, we treat it as a comment. We can augment the existing collected
     * text with the comment by replacing it, rather than expecting all Handlers to handler both scenarios
     *
     * @param Node\Expr $keyNode
     * @param Node\Expr $extra
     * @return boolean Whether an action was performed
     */
    protected function augmentWithComments(Node\Expr $keyNode, Node\Expr $extra): bool
    {
        /** @var Node\Scalar\String_ $keyNode */
        if (!$keyNode instanceof Node\Scalar\String_
            || !$extra instanceof Node\Scalar\String_
            || !$this->repository->has($keyNode->value)
        ) {
            return false;
        }

        $this->repository->addArray($keyNode->value, [
            'default' => $this->repository->get($keyNode->value),
            'comment' => $extra->value,
        ]);
        return true;
    }
}
