<?php

declare(strict_types=1);

namespace Facile\ZFLinkHeadersModule;

use Zend\Stdlib\AbstractOptions;

final class Options extends AbstractOptions implements OptionsInterface
{
    /**
     * @var bool
     */
    private $stylesheetEnabled = false;
    /**
     * @var string
     */
    private $stylesheetMode = self::MODE_PRELOAD;
    /**
     * @var bool
     */
    private $scriptEnabled = false;
    /**
     * @var string
     */
    private $scriptMode = self::MODE_PRELOAD;
    /**
     * @var bool
     */
    private $http2PushEnabled = false;

    /**
     * @return bool
     */
    public function isStylesheetEnabled(): bool
    {
        return $this->stylesheetEnabled;
    }

    /**
     * @param bool $stylesheetEnabled
     */
    public function setStylesheetEnabled(bool $stylesheetEnabled)
    {
        $this->stylesheetEnabled = $stylesheetEnabled;
    }

    /**
     * @return string
     */
    public function getStylesheetMode(): string
    {
        return $this->stylesheetMode;
    }

    /**
     * @param string $stylesheetMode
     */
    public function setStylesheetMode(string $stylesheetMode)
    {
        $this->stylesheetMode = $stylesheetMode;
    }

    /**
     * @return bool
     */
    public function isHttp2PushEnabled(): bool
    {
        return $this->http2PushEnabled;
    }

    /**
     * @param bool $http2PushEnabled
     */
    public function setHttp2PushEnabled(bool $http2PushEnabled)
    {
        $this->http2PushEnabled = $http2PushEnabled;
    }

    /**
     * @return bool
     */
    public function isScriptEnabled(): bool
    {
        return $this->scriptEnabled;
    }

    /**
     * @param bool $scriptEnabled
     */
    public function setScriptEnabled(bool $scriptEnabled)
    {
        $this->scriptEnabled = $scriptEnabled;
    }

    /**
     * @return string
     */
    public function getScriptMode(): string
    {
        return $this->scriptMode;
    }

    /**
     * @param string $scriptMode
     */
    public function setScriptMode(string $scriptMode)
    {
        $this->scriptMode = $scriptMode;
    }
}
