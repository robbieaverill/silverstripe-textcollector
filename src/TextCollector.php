<?php

namespace SilverStripe\TextCollector;

use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;

class TextCollector
{
    use Configurable;
    use Injectable;

    private static $dependencies = [
        'CodeCollector' => '%$' . CollectorInterface::class . '.code',
        'TemplateCollector' => '%$' . CollectorInterface::class . '.template',
    ];

    /**
     * @var CollectorInterface
     */
    protected $codeCollector;

    /**
     * @var CollectorInterface
     */
    protected $templateCollector;

    /**
     * @return CollectorInterface
     */
    public function getCodeCollector(): CollectorInterface
    {
        return $this->codeCollector;
    }

    /**
     * @param CollectorInterface $codeCollector
     * @return $this
     */
    public function setCodeCollector(CollectorInterface $codeCollector)
    {
        $this->codeCollector = $codeCollector;
        return $this;
    }

    /**
     * @return CollectorInterface
     */
    public function getTemplateCollector(): CollectorInterface
    {
        return $this->templateCollector;
    }

    /**
     * @param CollectorInterface $templateCollector
     * @return $this
     */
    public function setTemplateCollector(CollectorInterface $templateCollector)
    {
        $this->templateCollector = $templateCollector;
        return $this;
    }
}
