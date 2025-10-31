<?php

declare(strict_types=1);

namespace Tourze\EasyAdminEnumFieldBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\EasyAdminEnumFieldBundle\EasyAdminEnumFieldBundle;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(EasyAdminEnumFieldBundle::class)]
#[RunTestsInSeparateProcesses]
final class EasyAdminEnumFieldBundleTest extends AbstractBundleTestCase
{
}
