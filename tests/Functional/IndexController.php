<?php

declare(strict_types=1);

namespace Facile\ZFLinkHeadersModule\Functional;

use Zend\Mvc\Controller\AbstractActionController;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $this->layout()->setTemplate('index');
    }
}
