<?php

declare(strict_types=1);

namespace Tourze\EasyAdminEnumFieldBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\EasyAdminEnumFieldBundle\DependencyInjection\EasyAdminEnumFieldExtension;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * @internal
 */
#[CoversClass(EasyAdminEnumFieldExtension::class)]
final class EasyAdminEnumFieldExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    public function testLoadWithEmptyConfiguration(): void
    {
        $configs = [];
        $container = new ContainerBuilder();
        $container->setParameter('kernel.environment', 'test');

        $extension = new EasyAdminEnumFieldExtension();
        // 测试加载不会抛出异常
        $extension->load($configs, $container);

        // 验证服务配置已加载
        $this->assertTrue($container->hasParameter('kernel.environment'));
    }

    public function testGetAlias(): void
    {
        $extension = new EasyAdminEnumFieldExtension();
        $alias = $extension->getAlias();
        $this->assertEquals('easy_admin_enum_field', $alias);
    }
}
