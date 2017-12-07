<?php

declare(strict_types=1);

namespace Facile\ZFLinkHeadersModule\Listener;

use Zend\Mvc\MvcEvent;

interface LinkHandlerInterface
{
    /**
     * @param MvcEvent $event
     */
    public function __invoke(MvcEvent $event);
}
