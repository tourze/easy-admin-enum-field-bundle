<?php

declare(strict_types=1);

namespace Tourze\EasyAdminEnumFieldBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;
use Tourze\EasyAdminEnumFieldBundle\Service\EnumFieldFactory;

/**
 * @internal
 */
#[CoversClass(EnumFieldFactory::class)]
final class EnumFieldFactoryTest extends TestCase
{
    private EnumFieldFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new EnumFieldFactory();
    }

    #[Test]
    public function testCreateEnumFieldWithoutOptions(): void
    {
        $property = 'status';
        $label = 'Status';
        $enumCases = [];

        $field = $this->factory->createEnumField($property, $label, $enumCases);

        self::assertSame($property, $field->getAsDto()->getProperty());
        self::assertSame($label, $field->getAsDto()->getLabel());
    }

    #[Test]
    public function testCreateEnumFieldWithOptions(): void
    {
        $property = 'status';
        $label = 'Status';
        $enumCases = [];
        $options = [
            'required' => true,
            'custom_option' => 'value',
        ];

        $field = $this->factory->createEnumField($property, $label, $enumCases, $options);

        self::assertSame($property, $field->getAsDto()->getProperty());
        self::assertSame($label, $field->getAsDto()->getLabel());

        // 验证 required 选项是否正确设置
        $formOptions = $field->getAsDto()->getFormTypeOptions();
        self::assertTrue($formOptions['required'] ?? false);
    }

    #[Test]
    public function testCreateEnumFieldWithRequiredFalse(): void
    {
        $property = 'status';
        $label = 'Status';
        $enumCases = [];
        $options = ['required' => false];

        $field = $this->factory->createEnumField($property, $label, $enumCases, $options);

        $formOptions = $field->getAsDto()->getFormTypeOptions();
        self::assertFalse($formOptions['required'] ?? true);
    }

    #[Test]
    public function testCreateEnumFieldWithStringRequired(): void
    {
        $property = 'status';
        $label = 'Status';
        $enumCases = [];
        $options = ['required' => '1'];

        $field = $this->factory->createEnumField($property, $label, $enumCases, $options);

        // 验证字符串 '1' 被正确转换为布尔值 true
        $formOptions = $field->getAsDto()->getFormTypeOptions();
        self::assertTrue($formOptions['required'] ?? false);
    }

    #[Test]
    public function testConfigureEnumCases(): void
    {
        $field = EnumField::new('status');
        $enumCases = [];

        $result = $this->factory->configureEnumCases($field, $enumCases);

        self::assertSame($field, $result);
    }

    #[Test]
    public function testConfigureEnumCasesWithEmptyArray(): void
    {
        $field = EnumField::new('status');
        $enumCases = [];

        $result = $this->factory->configureEnumCases($field, $enumCases);

        self::assertSame($field, $result);
    }
}