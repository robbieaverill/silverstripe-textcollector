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

        $this->augmentWithComments($keyNode->value, $node->args);
        $this->handlePlurals();
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
     * text with the comment by replacing it, rather than expecting all Handlers to handle both scenarios
     *
     * @param Node\Expr $keyNode
     * @param Node[] $args
     * @return boolean Whether an action was performed
     */
    protected function augmentWithComments(Node\Expr $keyNode, array $args): bool
    {
        /** @var Node\Scalar\String_ $keyNode */
        if (!$keyNode instanceof Node\Scalar\String_ || !$this->repository->has($keyNode->value)) {
            return false;
        }

        // Look for a string node. If one is found, use it.
        $extraArgs = array_slice($args, 2);
        $comment = false;
        foreach ($extraArgs as $extraArg) {
            if ($extraArg->value instanceof Node\Scalar\String_) {
                $comment = $extraArg->value;
                break;
            }
        }

        // Skip if no comment was found
        if (!$comment) {
            return false;
        }

        $this->repository->addArray($keyNode->value, [
            'default' => $this->repository->get($keyNode->value),
            'comment' => $comment->value,
        ]);
        return true;
    }

    /**
     * Processes all items in the text repository and looks for translations that should be treated as pluralisations,
     * e.g. `An item|{count} items`, and breaks them up into structured data.
     */
    protected function handlePlurals(): void
    {
        foreach ($this->repository->getAll() as $key => $value) {
            $checkValue = is_string($value) ? $value : $value['default'];
            // Look for a pipe delimiter, indicating pluralisation
            if (strpos($checkValue, '|') === false) {
                continue;
            }

            list($single, $plural) = explode('|', $checkValue);
            $originalArray = is_array($value) ? $value : [];
            unset($originalArray['default']);

            $this->repository->addArray($key, [
                'one' => $single,
                'other' => $plural,
            ] + $originalArray);
        }
    }
}
