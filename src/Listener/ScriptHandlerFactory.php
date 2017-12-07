<?php

declare(strict_types=1);

namespace Facile\ZFLinkHeadersModule\Listener;

use Facile\ZFLinkHeadersModule\OptionsInterface;
use Psr\Container\ContainerInterface;
use Zend\View\Helper\HeadScript;
use Zend\View\HelperPluginManager;

/**
 * Class ScriptHandlerFactory
 */
class ScriptHandlerFactory
{
    /**
     * @param ContainerInterface $container
     * @return ScriptHandler
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container)
    {
        /** @var HelperPluginManager $plugins */
        $plugins = $container->get('ViewHelperManager');
        /** @var HeadScript $headScript */
        $headScript = $plugins->get(HeadScript::class);

        $options = $container->get(OptionsInterface::class);

        return new ScriptHandler($headScript, $options);
    }
}
