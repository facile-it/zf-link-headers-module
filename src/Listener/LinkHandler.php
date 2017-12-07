<?php

declare(strict_types=1);

namespace Facile\ZFLinkHeadersModule\Listener;

use Facile\ZFLinkHeadersModule\Exception;
use Facile\ZFLinkHeadersModule\OptionsInterface;
use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\MvcEvent;
use Zend\View\Helper\HeadLink;

/**
 * Class LinkHandler
 */
final class LinkHandler extends AbstractLinkHandler
{
    const ALLOWED_RELS = [
        OptionsInterface::MODE_PRELOAD,
        OptionsInterface::MODE_PREFETCH,
        OptionsInterface::MODE_DNS_PREFETCH,
        OptionsInterface::MODE_PRECONNECT,
        OptionsInterface::MODE_PRERENDER,
    ];

    /**
     * @var HeadLink
     */
    private $headLink;

    /**
     * @var OptionsInterface
     */
    private $options;

    /**
     * LinkHandler constructor.
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
        $response = $event->getResponse();
        if (! $response instanceof Response) {
            return;
        }

        $values = [];

        foreach ($this->headLink->getContainer() as $item) {
            $attributes = \get_object_vars($item);

            if (! $this->canInjectLink($attributes)) {
                continue;
            }

            if (! $this->options->isHttp2PushEnabled()) {
                $attributes['nopush'] = null;
            }

            $values[] = $this->createLinkHeaderValue($attributes);
        }

        $this->addLinkHeader($response, $values);
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

        return \in_array($attributes['rel'] ?? '', self::ALLOWED_RELS, true);
    }
}
