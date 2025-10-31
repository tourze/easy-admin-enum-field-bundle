<?php

declare(strict_types=1);

namespace Tourze\EasyAdminEnumFieldBundle\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;
use Tourze\EnumExtra\BadgeInterface;

/**
 * @implements Rule<Node\Expr\MethodCall>
 */
class EnumFieldCasesImplementsBadgeInterfaceRule implements Rule
{
    private ReflectionProvider $reflectionProvider;

    public function __construct(ReflectionProvider $reflectionProvider)
    {
        $this->reflectionProvider = $reflectionProvider;
    }

    public function getNodeType(): string
    {
        return Node\Expr\MethodCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node instanceof Node\Expr\MethodCall) {
            return [];
        }

        if (0 === count($node->getArgs())) {
            return [];
        }

        $methodName = $node->name;
        if (!$methodName instanceof Node\Identifier || 'setEnumCases' !== $methodName->toString()) {
            return [];
        }

        $callerType = $scope->getType($node->var);
        $isEnumField = (new ObjectType(EnumField::class))->isSuperTypeOf($callerType)->yes();

        if (!$isEnumField) {
            return [];
        }

        $arg = $node->getArgs()[0];
        $argValue = $arg->value;

        if (!$argValue instanceof Node\Expr\StaticCall) {
            return [];
        }

        $staticCall = $argValue;
        $staticCallMethodName = $staticCall->name;

        if (!$staticCallMethodName instanceof Node\Identifier || 'cases' !== $staticCallMethodName->toString()) {
            return [];
        }

        $classNameNode = $staticCall->class;
        if (!$classNameNode instanceof Node\Name) {
            return [];
        }
        $className = $scope->resolveName($classNameNode);

        if (!$this->reflectionProvider->hasClass($className)) {
            return [];
        }

        $classReflection = $this->reflectionProvider->getClass($className);

        if (!$classReflection->isEnum()) {
            return [];
        }

        if ($classReflection->implementsInterface(BadgeInterface::class)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                sprintf(
                    '枚举类 %s 必须实现 %s ，以提升 EnumField::setEnumCases() 的渲染效果',
                    $className,
                    BadgeInterface::class
                )
            )->build(),
        ];
    }
}
