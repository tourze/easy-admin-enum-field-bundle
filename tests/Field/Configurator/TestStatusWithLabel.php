<?php

declare(strict_types=1);

namespace Tourze\EasyAdminEnumFieldBundle\Tests\Field\Configurator;

use Tourze\EnumExtra\Labelable;

/**
 * @internal 实现Labelable接口的测试枚举
 */
enum TestStatusWithLabel implements Labelable
{
    case ACTIVE;
    case INACTIVE;

    public function getLabel(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active Status',
            self::INACTIVE => 'Inactive Status',
        };
    }
}
