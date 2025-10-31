<?php

declare(strict_types=1);

namespace Tourze\EasyAdminEnumFieldBundle\Tests\Field\Configurator;

use Tourze\EnumExtra\BadgeInterface;

/**
 * @internal 实现BadgeInterface接口的测试枚举
 */
enum TestStatusWithBadge implements BadgeInterface
{
    case ACTIVE;
    case INACTIVE;

    public function getBadge(): string
    {
        return match ($this) {
            self::ACTIVE => self::SUCCESS,
            self::INACTIVE => self::DANGER,
        };
    }
}
