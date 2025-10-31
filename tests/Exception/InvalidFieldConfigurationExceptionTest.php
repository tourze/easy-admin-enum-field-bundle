<?php

declare(strict_types=1);

namespace Tourze\EasyAdminEnumFieldBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\EasyAdminEnumFieldBundle\Exception\InvalidFieldConfigurationException;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(InvalidFieldConfigurationException::class)]
final class InvalidFieldConfigurationExceptionTest extends AbstractExceptionTestCase
{
    public function testWithCodeAndPrevious(): void
    {
        $previousException = new \Exception('Previous exception');
        $exception = new InvalidFieldConfigurationException('Test message', 123, $previousException);

        self::assertSame('Test message', $exception->getMessage());
        self::assertSame(123, $exception->getCode());
        self::assertSame($previousException, $exception->getPrevious());
    }
}
