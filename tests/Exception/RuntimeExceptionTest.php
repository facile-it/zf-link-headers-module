<?php

declare(strict_types=1);

namespace Facile\ZFLinkHeadersModule\Exception;

use PHPUnit\Framework\TestCase;

class RuntimeExceptionTest extends TestCase
{
    public function testImplementsInterface()
    {
        $exception = new RuntimeException();

        $this->assertInstanceOf(ExceptionInterface::class, $exception);
        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }
}
