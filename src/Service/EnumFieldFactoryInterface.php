<?php

declare(strict_types=1);

namespace Tourze\EasyAdminEnumFieldBundle\Service;

use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;

interface EnumFieldFactoryInterface
{
    /**
     * 创建枚举字段
     *
     * @param string $property 属性名
     * @param string $label 字段标签
     * @param array<\UnitEnum> $enumCases 枚举选项
     * @param array<string, mixed> $options 额外选项
     */
    public function createEnumField(string $property, string $label, array $enumCases, array $options = []): EnumField;

    /**
     * 配置现有字段的枚举选项
     *
     * @param array<\UnitEnum> $enumCases 枚举选项
     */
    public function configureEnumCases(EnumField $field, array $enumCases): EnumField;
}