<?php

declare(strict_types=1);

namespace Tourze\EasyAdminEnumFieldBundle\Field\Configurator;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\Configurator\ChoiceConfigurator;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;
use Tourze\EnumExtra\Labelable;

/**
 * 让自定义的 EnumField 复用 EasyAdmin 原生 ChoiceConfigurator 的全部能力。
 * 通过组合而非继承（ChoiceConfigurator 为 final）。
 */
final class EnumFieldConfigurator implements FieldConfiguratorInterface
{
    private ChoiceConfigurator $choiceConfigurator;

    public function __construct()
    {
        $this->choiceConfigurator = new ChoiceConfigurator();
    }

    public function supports(FieldDto $field, EntityDto $entityDto): bool
    {
        return EnumField::class === $field->getFieldFqcn();
    }

    public function configure(FieldDto $field, EntityDto $entityDto, AdminContext $context): void
    {
        $value = $field->getValue();
        $page = $context->getCrud()?->getCurrentPage();
        $isDisplayPage = \in_array($page, [Crud::PAGE_INDEX, Crud::PAGE_DETAIL], true);
        $badgeSelector = $field->getCustomOption(EnumField::OPTION_RENDER_AS_BADGES);

        if ($isDisplayPage && null !== $badgeSelector) {
            $this->configureForBadgeDisplay($field, $value, $badgeSelector);
        } else {
            $this->configureForPlainDisplay($field, $value);
        }

        $this->choiceConfigurator->configure($field, $entityDto, $context);
    }

    private function configureForBadgeDisplay(FieldDto $field, mixed $value, mixed $badgeSelector): void
    {
        if (\is_iterable($value)) {
            $this->renderIterableValue($field, $value, $badgeSelector);
        } elseif (null !== $value) {
            $this->renderSingleValue($field, $value, $badgeSelector);
        }
    }

    private function configureForPlainDisplay(FieldDto $field, mixed $value): void
    {
        if ($value instanceof \UnitEnum) {
            $label = $value instanceof Labelable ? $value->getLabel() : $value->name;
            $field->setFormattedValue($label);
        } elseif (\is_iterable($value)) {
            $this->renderIterableValueAsPlainText($field, $value);
        }
    }

    /**
     * @param iterable<mixed> $value
     */
    private function renderIterableValueAsPlainText(FieldDto $field, iterable $value): void
    {
        $labels = [];
        foreach ($value as $item) {
            $labels[] = $this->convertItemToLabel($item);
        }
        $field->setFormattedValue(implode(', ', $labels));
    }

    private function convertItemToLabel(mixed $item): string
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

        return \is_object($item) ? \get_class($item) : \gettype($item);
    }

    /**
     * @param iterable<mixed> $value
     */
    private function renderIterableValue(FieldDto $field, iterable $value, mixed $badgeSelector): void
    {
        $parts = [];
        foreach ($value as $item) {
            $parts[] = $this->renderSingleItem($item, $badgeSelector, $field);
        }
        $field->setFormattedValue(implode(', ', $parts));
    }

    private function renderSingleValue(FieldDto $field, mixed $value, mixed $badgeSelector): void
    {
        $rendered = $this->renderSingleItem($value, $badgeSelector, $field);
        $field->setFormattedValue($rendered);
    }

    private function renderSingleItem(mixed $item, mixed $badgeSelector, FieldDto $field): string
    {
        $label = $this->extractLabel($item);
        $keyForBadge = $this->extractBadgeKey($item);
        $css = $this->calculateBadgeCss($badgeSelector, $keyForBadge, $field);

        return '' !== $css ? sprintf('<span class="%s">%s</span>', $css, $label) : $label;
    }

    private function extractLabel(mixed $item): string
    {
        if ($item instanceof \UnitEnum) {
            return $item instanceof Labelable ? $item->getLabel() : $item->name;
        }

        if (null === $item) {
            return '';
        }

        if (\is_scalar($item)) {
            return (string) $item;
        }

        return '';
    }

    private function extractBadgeKey(mixed $item): string
    {
        if ($item instanceof \BackedEnum) {
            return (string) $item->value;
        }

        if ($item instanceof \UnitEnum) {
            return $item->name;
        }

        if (null === $item) {
            return '';
        }

        if (\is_scalar($item)) {
            return (string) $item;
        }

        return '';
    }

    private function calculateBadgeCss(mixed $badgeSelector, string $keyForBadge, FieldDto $field): string
    {
        if (true === $badgeSelector) {
            return 'badge badge-secondary';
        }

        if (\is_array($badgeSelector)) {
            /** @var array<string, string> $badgeSelector */
            return $this->calculateArrayBadgeCss($badgeSelector, $keyForBadge);
        }

        if (\is_callable($badgeSelector)) {
            return $this->calculateCallableBadgeCss($badgeSelector, $keyForBadge, $field);
        }

        return '';
    }

    /**
     * @param array<string, string> $badgeSelector
     */
    private function calculateArrayBadgeCss(array $badgeSelector, string $keyForBadge): string
    {
        $type = $badgeSelector[$keyForBadge] ?? 'secondary';
        if (!\in_array($type, EnumField::VALID_BADGE_TYPES, true)) {
            $type = 'secondary';
        }

        return 'badge badge-' . $type;
    }

    private function calculateCallableBadgeCss(callable $badgeSelector, string $keyForBadge, FieldDto $field): string
    {
        $type = (string) $badgeSelector($keyForBadge, $field);
        $validatedType = \in_array($type, EnumField::VALID_BADGE_TYPES, true) ? $type : 'secondary';

        return 'badge badge-' . $validatedType;
    }
}
