<?php

declare(strict_types=1);

namespace Tourze\EasyAdminEnumFieldBundle\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Contracts\Translation\TranslatableInterface;
use Tourze\EasyAdminEnumFieldBundle\Exception\InvalidFieldConfigurationException;
use Tourze\EnumExtra\BadgeInterface;
use Tourze\EnumExtra\Labelable;

/**
 * 复制 vendor/easycorp/easyadmin-bundle/src/Field/ChoiceField.php
 */
class EnumField implements FieldInterface
{
    use FieldTrait;

    public const OPTION_ALLOW_MULTIPLE_CHOICES = 'allowMultipleChoices';
    public const OPTION_AUTOCOMPLETE = 'autocomplete';
    public const OPTION_CHOICES = 'choices';
    public const OPTION_USE_TRANSLATABLE_CHOICES = 'useTranslatableChoices';
    public const OPTION_RENDER_AS_BADGES = 'renderAsBadges';
    public const OPTION_RENDER_EXPANDED = 'renderExpanded';
    public const OPTION_WIDGET = 'widget';
    public const OPTION_ESCAPE_HTML_CONTENTS = 'escapeHtml';

    public const VALID_BADGE_TYPES = ['success', 'warning', 'danger', 'info', 'primary', 'secondary', 'light', 'dark'];

    public const WIDGET_AUTOCOMPLETE = 'autocomplete';
    public const WIDGET_NATIVE = 'native';

    /**
     * @param TranslatableInterface|string|false|null $label
     */
    public static function new(string $propertyName, $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplateName('crud/field/choice')
            ->setFormType(ChoiceType::class)
            ->addCssClass('field-select')
            ->setDefaultColumns('') // this is set dynamically in the field configurator
            ->setCustomOption(self::OPTION_CHOICES, null)
            ->setCustomOption(self::OPTION_USE_TRANSLATABLE_CHOICES, false)
            ->setCustomOption(self::OPTION_ALLOW_MULTIPLE_CHOICES, false)
            ->setCustomOption(self::OPTION_RENDER_AS_BADGES, null)
            ->setCustomOption(self::OPTION_RENDER_EXPANDED, false)
            ->setCustomOption(self::OPTION_WIDGET, null)
            ->setCustomOption(self::OPTION_ESCAPE_HTML_CONTENTS, true)
        ;
    }

    public function allowMultipleChoices(bool $allow = true): self
    {
        $this->setCustomOption(self::OPTION_ALLOW_MULTIPLE_CHOICES, $allow);

        return $this;
    }

    public function autocomplete(): self
    {
        $this->setCustomOption(self::OPTION_AUTOCOMPLETE, true);

        return $this;
    }

    /**
     * Given choices must follow the same format used in Symfony Forms:
     * ['Label visible to users' => 'submitted_value', ...].
     *
     * 如果选择值的内容是可翻译对象，请改用
     * setTranslatableChoices() 方法：
     * ['submitted_value' => t('Label visible to users'), ...].
     *
     * 除了数组之外，你还可以使用 PHP 回调函数，该函数接收当前实体的实例
     * （可能为 null）和 FieldDto 作为第二个参数：
     * ->setChoices(fn () => ['foo' => 1, 'bar' => 2])
     * ->setChoices(fn (?MyEntity $foo) => $foo->someField()->getChoices())
     * ->setChoices(fn (?MyEntity $foo, FieldDto $field) => ...)
     * @param mixed $choiceGenerator
     */
    public function setChoices($choiceGenerator): void
    {
        if (!\is_array($choiceGenerator) && !\is_callable($choiceGenerator)) {
            throw new InvalidFieldConfigurationException(sprintf('The argument of the "%s" method must be an array or a closure ("%s" given).', __METHOD__, \gettype($choiceGenerator)));
        }

        $this->setCustomOption(self::OPTION_CHOICES, $choiceGenerator);
    }

    /**
     * 当选择的内容使用可翻译对象时，你不能使用
     * setChoices() 方法，因为 PHP 不允许使用对象作为数组键。
     *
     * Given choices must follow the opposite of the format used in Symfony Forms:
     * ['submitted_value' => t('Label visible to users'), ...].
     * @param mixed $choiceGenerator
     */
    public function setTranslatableChoices($choiceGenerator): void
    {
        $this->setChoices($choiceGenerator);
        $this->setCustomOption(self::OPTION_USE_TRANSLATABLE_CHOICES, true);
    }

    /**
     * Possible values of $badgeSelector:
     *   * true: all values are displayed as 'secondary' badges
     *   * false: no badges are displayed; values are displayed as regular text
     *   * array: [$fieldValue => $badgeType, ...] (e.g. ['foo' => 'primary', 7 => 'warning', 'cancelled' => 'danger'])
     *   * callable: function(FieldDto $field): string { return '...' }
     *     (e.g. function(FieldDto $field) { return $field->getValue() < 10 ? 'warning' : 'primary'; }).
     *
     * Possible badge types: 'success', 'warning', 'danger', 'info', 'primary', 'secondary', 'light', 'dark'
     * @param mixed $badgeSelector - Should be bool|array<string, string>|callable
     */
    public function renderAsBadges($badgeSelector = true): self
    {
        $this->validateBadgeSelector($badgeSelector);
        $this->setCustomOption(self::OPTION_RENDER_AS_BADGES, $badgeSelector);

        return $this;
    }

    /**
     * @param mixed $badgeSelector - Should be bool|array<string, string>|callable
     */
    private function validateBadgeSelector(mixed $badgeSelector): void
    {
        if (!\is_bool($badgeSelector) && !\is_array($badgeSelector) && !\is_callable($badgeSelector)) {
            throw new InvalidFieldConfigurationException(sprintf('The argument of the "%s" method must be a boolean, an array or a closure ("%s" given).', __CLASS__ . '::renderAsBadges', \gettype($badgeSelector)));
        }

        if (\is_array($badgeSelector)) {
            /** @var array<string, string> $badgeSelector */
            $this->validateBadgeTypes($badgeSelector);
        }
    }

    /**
     * @param array<string, string> $badgeSelector
     */
    private function validateBadgeTypes(array $badgeSelector): void
    {
        foreach ($badgeSelector as $badgeType) {
            if (!\in_array($badgeType, self::VALID_BADGE_TYPES, true)) {
                throw new InvalidFieldConfigurationException(sprintf('The values of the array passed to the "%s" method must be one of the following valid badge types: "%s" ("%s" given).', __CLASS__ . '::renderAsBadges', implode(', ', self::VALID_BADGE_TYPES), $badgeType));
            }
        }
    }

    public function renderAsNativeWidget(bool $asNative = true): self
    {
        $this->setCustomOption(self::OPTION_WIDGET, $asNative ? self::WIDGET_NATIVE : self::WIDGET_AUTOCOMPLETE);

        return $this;
    }

    public function renderExpanded(bool $expanded = true): self
    {
        $this->setCustomOption(self::OPTION_RENDER_EXPANDED, $expanded);

        return $this;
    }

    public function escapeHtml(bool $escape = true): self
    {
        $this->setCustomOption(self::OPTION_ESCAPE_HTML_CONTENTS, $escape);

        return $this;
    }

    /**
     * @param \UnitEnum[] $cases
     */
    public function setEnumCases(array $cases): void
    {
        $choices = $this->buildChoicesFromCases($cases);
        $this->setChoices($choices);
        $this->configureChoiceLabel();
        $this->configureEnumFormatValue();
        $this->configureEnumTransformers($cases);
    }

    /**
     * @param \UnitEnum[] $cases
     * @return array<string, mixed>
     */
    private function buildChoicesFromCases(array $cases): array
    {
        $choices = [];
        foreach ($cases as $case) {
            $choices[$this->getEnumLabel($case)] = $case;
        }

        return $choices;
    }

    private function getEnumLabel(\UnitEnum $case): string
    {
        return $case instanceof Labelable ? $case->getLabel() : $case->name;
    }

    private function configureChoiceLabel(): void
    {
        $this->setFormTypeOption('choice_label', static function ($choice) {
            if ($choice instanceof \UnitEnum) {
                return $choice instanceof Labelable ? $choice->getLabel() : $choice->name;
            }

            if (\is_scalar($choice)) {
                return (string) $choice;
            }

            if (\is_object($choice) && method_exists($choice, '__toString')) {
                return (string) $choice;
            }

            // 对于无法转换为字符串的对象，使用其类型或类名作为fallback
            return \is_object($choice) ? \get_class($choice) : \gettype($choice);
        });
    }

    private function configureEnumFormatValue(): void
    {
        $this->formatValue(static function ($value) {
            if ($value instanceof \UnitEnum) {
                return $value instanceof Labelable ? $value->getLabel() : $value->name;
            }

            if (\is_iterable($value)) {
                return self::formatIterableValue($value);
            }

            return (string) $value;
        });
    }

    /**
     * @param iterable<mixed> $value
     */
    private static function formatIterableValue(iterable $value): string
    {
        $labels = [];
        foreach ($value as $item) {
            $labels[] = self::formatSingleValue($item);
        }

        return implode(', ', $labels);
    }

    /**
     * @param mixed $item
     */
    private static function formatSingleValue($item): string
    {
        if ($item instanceof \UnitEnum) {
            return $item instanceof Labelable ? $item->getLabel() : $item->name;
        }

        if (\is_scalar($item)) {
            return (string) $item;
        }

        if (\is_object($item) && method_exists($item, '__toString')) {
            return (string) $item;
        }

        // 对于无法转换为字符串的值，使用其类型表示作为fallback
        return \is_object($item) ? \get_class($item) : \gettype($item);
    }

    /**
     * @param array<\UnitEnum> $cases
     */
    private function configureEnumTransformers(array $cases): void
    {
        // 设置表单选项以确保正确的值转换
        $this->setFormTypeOption('choice_value', static function ($choice) {
            if ($choice instanceof \BackedEnum) {
                return $choice->value;
            }
            if ($choice instanceof \UnitEnum) {
                return $choice->name;
            }

            return (string) $choice;
        });
    }
}
