<?php

declare(strict_types=1);

namespace Facile\ZFLinkHeadersModule\Exception;

use PHPUnit\Framework\TestCase;

class InvalidArgumentExceptionTest extends TestCase
{
    public function testImplementsInterface()
    {
        $exception = new InvalidArgumentException();

        $this->assertInstanceOf(ExceptionInterface::class, $exception);
        $this->assertInstanceOf(\InvalidArgumentException::class, $exception);
    }
}
