<?php

declare(strict_types=1);

namespace Facile\ZFLinkHeadersModule;

interface OptionsInterface
{
    const MODE_PRELOAD = 'preload';
    const MODE_PREFETCH = 'prefetch';
    const MODE_DNS_PREFETCH = 'dns-prefetch';
    const MODE_PRECONNECT = 'preconnect';
    const MODE_PRERENDER = 'prerender';

    /**
     * @return bool
     */
    public function isStylesheetEnabled(): bool;

    /**
     * @return string
     */
    public function getStylesheetMode(): string;

    /**
     * @return bool
     */
    public function isScriptEnabled(): bool;

    /**
     * @return string
     */
    public function getScriptMode(): string;

    /**
     * @return bool
     */
    public function isHttp2PushEnabled(): bool;
}
