<?php

declare(strict_types=1);

namespace JMS\Serializer\Exclusion;

use JMS\Serializer\Context;
use JMS\Serializer\Expression\CompilableExpressionEvaluatorInterface;
use JMS\Serializer\Expression\Expression;
use JMS\Serializer\Expression\ExpressionEvaluatorInterface;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\SerializationContext;

/**
 * Exposes an exclusion strategy based on the Symfony's expression language.
 * This is not a standard exclusion strategy and can not be used in user applications.
 *
 * @internal
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 */
final class ExpressionLanguageExclusionStrategy
{
    /**
     * @var ExpressionEvaluatorInterface
     */
    private $expressionEvaluator;

    public function __construct(ExpressionEvaluatorInterface $expressionEvaluator)
    {
        $this->expressionEvaluator = $expressionEvaluator;
    }

    /**
     * {@inheritDoc}
     */
    public function shouldSkipProperty(PropertyMetadata $property, Context $navigatorContext): bool
    {
        if (null === $property->excludeIf) {
            return false;
        }

        $variables = [
            'context' => $navigatorContext,
            'property_metadata' => $property,
        ];
        if ($navigatorContext instanceof SerializationContext) {
            $variables['object'] = $navigatorContext->getObject();
        } else {
            $variables['object'] = null;
        }

        if (($property->excludeIf instanceof Expression) && ($this->expressionEvaluator instanceof CompilableExpressionEvaluatorInterface)) {
            return $this->expressionEvaluator->evaluateParsed($property->excludeIf, $variables);
        }

        return $this->expressionEvaluator->evaluate($property->excludeIf, $variables);
    }

    /**
     * {@inheritDoc}
     */
    public function shouldSkipPropertyOnDeserialize(PropertyMetadata $property, Context $navigatorContext): bool
    {
        if (null === $property->readOnlyIf) {
            return false;
        }

        $variables = [
            'context' => $navigatorContext,
            'property_metadata' => $property,
        ];
        $variables['object'] = null;

        if (($property->readOnlyIf instanceof Expression) && ($this->expressionEvaluator instanceof CompilableExpressionEvaluatorInterface)) {
            return $this->expressionEvaluator->evaluateParsed($property->readOnlyIf, $variables);
        }

        return $this->expressionEvaluator->evaluate($property->readOnlyIf, $variables);
    }
}
