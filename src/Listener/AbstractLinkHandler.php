<?php

declare(strict_types=1);

namespace Facile\ZFLinkHeadersModule\Listener;

use Zend\Http\Header\GenericMultiHeader;
use Zend\Http\Header\HeaderInterface;
use Zend\Http\Header\HeaderValue;
use Zend\Http\PhpEnvironment\Response;

abstract class AbstractLinkHandler implements LinkHandlerInterface
{
    const RFC_PARAMS = [
        'rel',
        'anchor',
        'rev',
        'hreflang',
        'media',
        'title',
        'type',
    ];

    /**
     * Get the attribute string for the header
     *
     * @param string $name
     * @param string $value
     * @return string
     */
    protected function getAttributeForHeader(string $name, $value): string
    {
        $name = \strtolower($name);
        // all RFC params must have a value, but not link-extensions
        if (null === $value && ! \in_array($name, self::RFC_PARAMS, true)) {
            return $name;
        }

        return \sprintf('%s="%s"', $name, \addslashes(HeaderValue::filter($value ?: null)));
    }

    /**
     * Returns the link header
     *
     * @param array $attributes
     * @return string
     */
    protected function createLinkHeaderValue(array $attributes): string
    {
        $href = $attributes['href'];
        unset($attributes['href']);

        $attributeLines = [];
        foreach ($attributes as $name => $value) {
            $attributeLines[] = $this->getAttributeForHeader($name, $value);
        }

        return \sprintf('<%s>; %s', $href, \implode('; ', $attributeLines));
    }

    /**
     * @param Response $response
     * @param array $values
     */
    protected function addLinkHeader(Response $response, array $values)
    {
        if (! \count($values)) {
            return;
        }

        $header = new GenericMultiHeader('Link', \implode(', ', $values));
        $currentHeader = $response->getHeaders()->get($header->getFieldName());

        /** @var HeaderInterface[] $headers */
        $headers = [];
        if ($currentHeader instanceof \ArrayIterator) {
            $headers = \iterator_to_array($currentHeader);
        } elseif (false !== $currentHeader) {
            $headers[] = $currentHeader;
        }

        foreach ($headers as $headerItem) {
            $response->getHeaders()->removeHeader($headerItem);
        }

        $headers[] = $header;

        $headerValues = \array_map(
            function (HeaderInterface $header) {
                return $header->getFieldValue();
            },
            $headers
        );

        $response->getHeaders()->addHeader(new GenericMultiHeader($header->getFieldName(), \implode(', ', $headerValues)));
    }
}
