# EasyAdmin 枚举字段扩展包

[English](README.md) | [中文](README.zh-CN.md)

[![PHP Version](https://img.shields.io/packagist/php-v/tourze/easy-admin-enum-field-bundle)](https://packagist.org/packages/tourze/easy-admin-enum-field-bundle)
[![Latest Version](https://img.shields.io/packagist/v/tourze/easy-admin-enum-field-bundle)](https://packagist.org/packages/tourze/easy-admin-enum-field-bundle)
[![License](https://img.shields.io/packagist/l/tourze/easy-admin-enum-field-bundle)](LICENSE)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/ci.yml)](https://github.com/tourze/php-monorepo/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/php-monorepo)](https://codecov.io/gh/tourze/php-monorepo)

一个 Symfony 扩展包，为 EasyAdmin 提供增强的枚举字段支持，
具有自动徽章渲染和高级显示选项。

## 功能特性

- 增强的枚举字段渲染，支持自动徽章显示
- 与 PHP 8.1+ 枚举无缝集成
- 从枚举案例自动生成标签
- 可自定义徽章颜色和样式
- 支持可翻译的枚举标签
- 多选功能支持
- 自动完成小部件支持
- 原生和展开渲染模式

## 安装

```bash
composer require tourze/easy-admin-enum-field-bundle
```

## 依赖要求

- PHP 8.1 或更高版本
- Symfony 6.4 或更高版本
- EasyAdmin Bundle 4.0 或更高版本
- tourze/enum-extra 包

## 快速开始

### 基本用法

```php
<?php

use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;
use App\Entity\StatusEnum;

// 在您的 EasyAdmin CrudController 中
public function configureFields(string $pageName): iterable
{
    yield EnumField::new('status')
        ->setEnumCases(StatusEnum::cases())
        ->renderAsBadges();
}
```

### 高级配置

```php
<?php

use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;

// 自定义徽章映射
yield EnumField::new('priority')
    ->setEnumCases(PriorityEnum::cases())
    ->renderAsBadges([
        'high' => 'danger',
        'medium' => 'warning',
        'low' => 'success'
    ]);

// 多选支持
yield EnumField::new('tags')
    ->setEnumCases(TagEnum::cases())
    ->allowMultipleChoices()
    ->autocomplete();
```

### 枚举接口支持

您的枚举可以实现 `Labelable` 和 `BadgeInterface` 接口以获得增强功能：

```php
<?php

use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\BadgeInterface;

enum StatusEnum: string implements Labelable, BadgeInterface
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case PENDING = 'pending';

    public function getLabel(): string
    {
        return match ($this) {
            self::ACTIVE => '活跃',
            self::INACTIVE => '非活跃',
            self::PENDING => '待处理',
        };
    }

    public function getBadge(): string
    {
        return match ($this) {
            self::ACTIVE => self::SUCCESS,
            self::INACTIVE => self::DANGER,
            self::PENDING => self::WARNING,
        };
    }
}
```

## 配置选项

### 徽章类型

可用的徽章类型：`success`、`warning`、`danger`、`info`、`primary`、`secondary`、`light`、`dark`

### 小部件类型

- `native`：使用原生 HTML 选择小部件
- `autocomplete`：使用具有搜索功能的自动完成小部件

### 显示选项

- `renderExpanded()`：渲染为单选按钮或复选框
- `escapeHtml()`：控制字段值中的 HTML 转义
- `allowMultipleChoices()`：启用多选功能

## 高级用法

### 自定义字段配置器

您可以创建自定义字段配置器以应用一致的枚举字段设置：

```php
<?php

use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;

class StatusFieldConfigurator
{
    public static function create(string $propertyName): EnumField
    {
        return EnumField::new($propertyName)
            ->setEnumCases(StatusEnum::cases())
            ->renderAsBadges([
                'active' => 'success',
                'inactive' => 'danger',
                'pending' => 'warning'
            ])
            ->escapeHtml(false);
    }
}
```

### 动态徽章配置

使用可调用函数进行动态徽章分配：

```php
yield EnumField::new('status')
    ->setEnumCases(StatusEnum::cases())
    ->renderAsBadges(function(FieldDto $field) {
        $value = $field->getValue();
        return match ($value) {
            StatusEnum::CRITICAL => 'danger',
            StatusEnum::HIGH => 'warning',
            default => 'info'
        };
    });
```

## 贡献

请查看 [CONTRIBUTING.md](CONTRIBUTING.md) 了解详情。

## 许可证

MIT 许可证 (MIT)。请查看 [License File](LICENSE) 了解更多信息。
