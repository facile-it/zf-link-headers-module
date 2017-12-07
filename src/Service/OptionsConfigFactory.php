<?php

declare(strict_types=1);

namespace Facile\ZFLinkHeadersModule\Service;

use Facile\ZFLinkHeadersModule\Options;
use Psr\Container\ContainerInterface;

final class OptionsConfigFactory
{
    /**
     * @param ContainerInterface $container
     * @return Options
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container)
    {
        /** @var array $config */
        $config = $container->get('config');

        $configOptions = $config['facile']['zf_link_headers_module'];

        return new Options($configOptions);
    }
}
