<?php

declare(strict_types=1);

namespace Tourze\EasyAdminEnumFieldBundle\Tests\Field;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\EasyAdminEnumFieldBundle\Exception\InvalidFieldConfigurationException;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;

/**
 * @internal
 */
#[CoversClass(EnumField::class)]
final class EnumFieldTest extends TestCase
{
    public function testNewInstance(): void
    {
        $field = EnumField::new('testProperty');

        self::assertSame('testProperty', $field->getAsDto()->getProperty());
        self::assertSame('testProperty', $field->getAsDto()->getProperty());
    }

    public function testSetChoicesWithArray(): void
    {
        $this->expectNotToPerformAssertions();

        $field = EnumField::new('status');
        $choices = ['Active' => 'active', 'Inactive' => 'inactive'];

        // setChoices method now returns void, so we just verify the method call doesn't throw
        $field->setChoices($choices);
    }

    public function testSetChoicesWithClosure(): void
    {
        $this->expectNotToPerformAssertions();

        $field = EnumField::new('status');
        $choicesGenerator = fn () => ['Active' => 'active', 'Inactive' => 'inactive'];

        // setChoices method now returns void, so we just verify the method call doesn't throw
        $field->setChoices($choicesGenerator);
    }

    public function testSetChoicesWithInvalidType(): void
    {
        $field = EnumField::new('status');

        $this->expectException(InvalidFieldConfigurationException::class);
        $this->expectExceptionMessage('The argument of the "Tourze\EasyAdminEnumFieldBundle\Field\EnumField::setChoices" method must be an array or a closure ("string" given).');

        $field->setChoices('invalid');
    }

    public function testRenderAsBadgesWithBoolean(): void
    {
        $field = EnumField::new('status');

        $result = $field->renderAsBadges(true);
        self::assertSame($field, $result);

        $result = $field->renderAsBadges(false);
        self::assertSame($field, $result);
    }

    public function testRenderAsBadgesWithValidArray(): void
    {
        $field = EnumField::new('status');
        $badgeSelector = ['active' => 'success', 'inactive' => 'danger'];

        $result = $field->renderAsBadges($badgeSelector);

        self::assertSame($field, $result);
    }

    public function testRenderAsBadgesWithInvalidBadgeType(): void
    {
        $field = EnumField::new('status');
        $badgeSelector = ['active' => 'invalid-badge-type'];

        $this->expectException(InvalidFieldConfigurationException::class);
        $this->expectExceptionMessage('The values of the array passed to the "Tourze\EasyAdminEnumFieldBundle\Field\EnumField::renderAsBadges" method must be one of the following valid badge types: "success, warning, danger, info, primary, secondary, light, dark" ("invalid-badge-type" given).');

        $field->renderAsBadges($badgeSelector);
    }

    public function testRenderAsBadgesWithInvalidType(): void
    {
        $field = EnumField::new('status');

        $this->expectException(InvalidFieldConfigurationException::class);
        $this->expectExceptionMessage('The argument of the "Tourze\EasyAdminEnumFieldBundle\Field\EnumField::renderAsBadges" method must be a boolean, an array or a closure ("string" given).');

        $field->renderAsBadges('invalid');
    }

    public function testAllowMultipleChoices(): void
    {
        $field = EnumField::new('tags');

        $result = $field->allowMultipleChoices();

        self::assertSame($field, $result);
    }

    public function testAutocomplete(): void
    {
        $field = EnumField::new('category');

        $result = $field->autocomplete();

        self::assertSame($field, $result);
    }

    public function testRenderExpanded(): void
    {
        $field = EnumField::new('options');

        $result = $field->renderExpanded();

        self::assertSame($field, $result);
    }

    public function testEscapeHtml(): void
    {
        $field = EnumField::new('content');

        $result = $field->escapeHtml(false);

        self::assertSame($field, $result);
    }

    public function testRenderAsNativeWidget(): void
    {
        $field = EnumField::new('type');

        $result = $field->renderAsNativeWidget();

        self::assertSame($field, $result);
    }

    public function testAddAssetMapperEntries(): void
    {
        $field = EnumField::new('test');

        $result = $field->addAssetMapperEntries('entry1', 'entry2');

        self::assertSame($field, $result);
    }

    public function testAddCssClass(): void
    {
        $field = EnumField::new('test');

        $result = $field->addCssClass('custom-class');

        self::assertSame($field, $result);
    }

    public function testAddCssFiles(): void
    {
        $field = EnumField::new('test');

        $result = $field->addCssFiles('style1.css', 'style2.css');

        self::assertSame($field, $result);
    }

    public function testAddFormTheme(): void
    {
        $field = EnumField::new('test');

        $result = $field->addFormTheme('theme1.html.twig', 'theme2.html.twig');

        self::assertSame($field, $result);
    }

    public function testAddHtmlContentsToBody(): void
    {
        $field = EnumField::new('test');

        $result = $field->addHtmlContentsToBody('<script>console.log("test");</script>');

        self::assertSame($field, $result);
    }

    public function testAddHtmlContentsToHead(): void
    {
        $field = EnumField::new('test');

        $result = $field->addHtmlContentsToHead('<meta name="test" content="value">');

        self::assertSame($field, $result);
    }

    public function testAddJsFiles(): void
    {
        $field = EnumField::new('test');

        $result = $field->addJsFiles('script1.js', 'script2.js');

        self::assertSame($field, $result);
    }

    public function testAddWebpackEncoreEntries(): void
    {
        $field = EnumField::new('test');

        try {
            $result = $field->addWebpackEncoreEntries('entry1', 'entry2');
            self::assertSame($field, $result);
        } catch (\RuntimeException $e) {
            self::assertStringContainsString('Webpack Encore is not installed', $e->getMessage());
        }
    }

    public function testFormatValue(): void
    {
        $field = EnumField::new('test');

        $result = $field->formatValue(fn ($value) => strtoupper((string) $value));

        self::assertSame($field, $result);
    }

    public function testHideOnDetail(): void
    {
        $field = EnumField::new('test');

        $result = $field->hideOnDetail();

        self::assertSame($field, $result);
    }

    public function testHideOnForm(): void
    {
        $field = EnumField::new('test');

        $result = $field->hideOnForm();

        self::assertSame($field, $result);
    }

    public function testHideOnIndex(): void
    {
        $field = EnumField::new('test');

        $result = $field->hideOnIndex();

        self::assertSame($field, $result);
    }

    public function testHideWhenCreating(): void
    {
        $field = EnumField::new('test');

        $result = $field->hideWhenCreating();

        self::assertSame($field, $result);
    }

    public function testHideWhenUpdating(): void
    {
        $field = EnumField::new('test');

        $result = $field->hideWhenUpdating();

        self::assertSame($field, $result);
    }

    public function testOnlyOnDetail(): void
    {
        $field = EnumField::new('test');

        $result = $field->onlyOnDetail();

        self::assertSame($field, $result);
    }

    public function testOnlyOnForms(): void
    {
        $field = EnumField::new('test');

        $result = $field->onlyOnForms();

        self::assertSame($field, $result);
    }

    public function testOnlyOnIndex(): void
    {
        $field = EnumField::new('test');

        $result = $field->onlyOnIndex();

        self::assertSame($field, $result);
    }

    public function testOnlyWhenCreating(): void
    {
        $field = EnumField::new('test');

        $result = $field->onlyWhenCreating();

        self::assertSame($field, $result);
    }

    public function testOnlyWhenUpdating(): void
    {
        $field = EnumField::new('test');

        $result = $field->onlyWhenUpdating();

        self::assertSame($field, $result);
    }

    public function testSetTranslatableChoices(): void
    {
        $this->expectNotToPerformAssertions();

        $field = EnumField::new('status');
        $choices = ['active' => 'Active', 'inactive' => 'Inactive'];

        // setTranslatableChoices method now returns void, so we just verify the method call doesn't throw
        $field->setTranslatableChoices($choices);
    }

    public function testSetEnumCases(): void
    {
        $this->expectNotToPerformAssertions();

        $field = EnumField::new('status');

        // setEnumCases method now returns void, so we just verify the method call doesn't throw
        $field->setEnumCases([]);
    }
}
