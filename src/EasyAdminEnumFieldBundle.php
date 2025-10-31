<?php

declare(strict_types=1);

namespace Tourze\EasyAdminEnumFieldBundle;

use EasyCorp\Bundle\EasyAdminBundle\EasyAdminBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;

class EasyAdminEnumFieldBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            TwigBundle::class => ['all' => true],
            EasyAdminBundle::class => ['all' => true],
        ];
    }
}
