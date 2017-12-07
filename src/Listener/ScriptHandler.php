<?php

declare(strict_types=1);

namespace Facile\ZFLinkHeadersModule\Listener;

use Facile\ZFLinkHeadersModule\Exception;
use Facile\ZFLinkHeadersModule\OptionsInterface;
use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\MvcEvent;
use Zend\View\Helper\HeadScript;

/**
 * Class ScriptHandler
 */
final class ScriptHandler extends AbstractLinkHandler
{
    /**
     * @var HeadScript
     */
    private $headScript;

    /**
     * @var OptionsInterface
     */
    private $options;

    /**
     * ScriptHandler constructor.
     *
     * @param HeadScript $headScript
     * @param OptionsInterface $options
     */
    public function __construct(HeadScript $headScript, OptionsInterface $options)
    {
        $this->headScript = $headScript;
        $this->options = $options;
    }

    /**
     * @param MvcEvent $event
     *
     * @throws Exception\InvalidArgumentException
     */
    public function __invoke(MvcEvent $event)
    {
        if (! $this->options->isScriptEnabled()) {
            return;
        }

        $response = $event->getResponse();
        if (! $response instanceof Response) {
            return;
        }

        foreach ($this->headScript->getContainer() as $item) {
            $properties = \get_object_vars($item);
            $attributes = $properties['attributes'] ?? [];

            if (! $this->canInjectLink($attributes)) {
                continue;
            }

            $attributes = [
                'href' => $attributes['src'],
                'rel' => $this->options->getScriptMode(),
                'as' => 'script',
                'type' => $properties['type'] ?? null,
            ];

            $attributes = \array_filter($attributes);

            if (! $this->options->isHttp2PushEnabled()) {
                $attributes['nopush'] = null;
            }

            $response->getHeaders()->addHeader($this->createLinkHeader($attributes));
        }
    }

    /**
     * Whether the link is valid to be injected in headers
     *
     * @param array $attributes
     * @return bool
     */
    private function canInjectLink(array $attributes): bool
    {
        return ! empty($attributes['src'] ?? '');
    }
}
