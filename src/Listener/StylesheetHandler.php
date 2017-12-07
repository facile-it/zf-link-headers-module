<?php

declare(strict_types=1);

namespace Facile\ZFLinkHeadersModule\Listener;

use Facile\ZFLinkHeadersModule\Exception;
use Facile\ZFLinkHeadersModule\OptionsInterface;
use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\MvcEvent;
use Zend\View\Helper\HeadLink;

/**
 * Class StylesheetHandler
 */
final class StylesheetHandler extends AbstractLinkHandler
{
    const TYPE_STYLESHEET = 'stylesheet';
    /**
     * @var HeadLink
     */
    private $headLink;

    /**
     * @var OptionsInterface
     */
    private $options;

    /**
     * StylesheetHandler constructor.
     *
     * @param HeadLink $headLink
     * @param OptionsInterface $options
     */
    public function __construct(HeadLink $headLink, OptionsInterface $options)
    {
        $this->headLink = $headLink;
        $this->options = $options;
    }

    /**
     * @param MvcEvent $event
     *
     * @throws Exception\InvalidArgumentException
     */
    public function __invoke(MvcEvent $event)
    {
        if (! $this->options->isStylesheetEnabled()) {
            return;
        }

        $response = $event->getResponse();
        if (! $response instanceof Response) {
            return;
        }

        foreach ($this->headLink->getContainer() as $item) {
            $attributes = $array = \get_object_vars($item);

            if (! $this->canInjectLink($attributes)) {
                continue;
            }

            $attributes = [
                'href' => $attributes['href'],
                'rel' => $this->options->getStylesheetMode(),
                'as' => 'script',
                'type' => $attributes['type'] ?? null,
                'media' => $attributes['media'] ?? null,
            ];

            $attributes = \array_filter($attributes);

            $attributes['rel'] = $this->options->getStylesheetMode();
            $attributes['as'] = 'style';

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
        if (empty($attributes['href'] ?? '')) {
            return false;
        }

        return self::TYPE_STYLESHEET === ($attributes['rel'] ?? '');
    }
}
