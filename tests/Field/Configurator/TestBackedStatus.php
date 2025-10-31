<?php

declare(strict_types=1);

namespace Tourze\EasyAdminEnumFieldBundle\Tests\Field\Configurator;

/**
 * @internal 带值的测试枚举
 */
enum TestBackedStatus: int
{
    case ACTIVE = 1;
    case INACTIVE = 0;
}
