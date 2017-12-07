<?php

declare(strict_types=1);

namespace Facile\ZFLinkHeadersModule\Listener;

use Facile\ZFLinkHeadersModule\OptionsInterface;
use Psr\Container\ContainerInterface;
use Zend\View\Helper\HeadLink;
use Zend\View\HelperPluginManager;

/**
 * Class LinkHandlerFactory
 */
class LinkHandlerFactory
{
    /**
     * @param ContainerInterface $container
     * @return LinkHandler
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container)
    {
        /** @var HelperPluginManager $plugins */
        $plugins = $container->get('ViewHelperManager');
        /** @var HeadLink $headLink */
        $headLink = $plugins->get(HeadLink::class);

        $options = $container->get(OptionsInterface::class);

        return new LinkHandler($headLink, $options);
    }
}
