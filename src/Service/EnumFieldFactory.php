<?php

declare(strict_types=1);

namespace Tourze\EasyAdminEnumFieldBundle\Service;

use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;

final class EnumFieldFactory implements EnumFieldFactoryInterface
{
    public function createEnumField(string $property, string $label, array $enumCases, array $options = []): EnumField
    {
        $field = EnumField::new($property, $label);
        $this->configureEnumCases($field, $enumCases);

        // 应用额外选项
        if (isset($options['required'])) {
            $field->setRequired((bool) $options['required']);
        }

        return $field;
    }

    /**
     * @param array<\UnitEnum> $enumCases
     */
    public function configureEnumCases(EnumField $field, array $enumCases): EnumField
    {
        $field->setEnumCases($enumCases);
        return $field;
    }
}