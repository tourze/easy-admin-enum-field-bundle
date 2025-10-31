<?php

declare(strict_types=1);

namespace Tourze\EasyAdminEnumFieldBundle\DependencyInjection;

use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

class EasyAdminEnumFieldExtension extends AutoExtension
{
    protected function getConfigDir(): string
    {
        return __DIR__ . '/../Resources/config';
    }
}
