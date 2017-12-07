<?php

declare(strict_types=1);

namespace Facile\ZFLinkHeadersModule\Listener;

use Zend\Http\Header\GenericMultiHeader;
use Zend\Http\Header\HeaderInterface;
use Zend\Http\Header\HeaderValue;

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
     * @return HeaderInterface
     */
    protected function createLinkHeader(array $attributes): HeaderInterface
    {
        $href = $attributes['href'];
        unset($attributes['href']);

        $attributeLines = [];
        foreach ($attributes as $name => $value) {
            $attributeLines[] = $this->getAttributeForHeader($name, $value);
        }

        $value = \sprintf('<%s>; %s', $href, \implode('; ', $attributeLines));

        return new GenericMultiHeader('Link', $value);
    }
}
