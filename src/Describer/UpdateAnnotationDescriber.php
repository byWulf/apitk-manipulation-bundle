<?php

declare(strict_types=1);

namespace Shopping\ApiTKManipulationBundle\Describer;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Nelmio\ApiDocBundle\Describer\ModelRegistryAwareInterface;
use Nelmio\ApiDocBundle\Describer\ModelRegistryAwareTrait;
use Nelmio\ApiDocBundle\Model\Model;
use Nelmio\ApiDocBundle\OpenApiPhp\Util;
use Nelmio\ApiDocBundle\RouteDescriber\RouteDescriberInterface;
use ReflectionMethod;
use Shopping\ApiTKCommonBundle\Describer\AbstractDescriber;
use Shopping\ApiTKCommonBundle\Describer\RouteDescriberTrait;
use Shopping\ApiTKManipulationBundle\Annotation\Update;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use OpenApi\Annotations as OA;
use ReflectionAttribute;

/**
 * Provides automatic @OA\Parameter OpenApi annotations for actions that use the
 * param converter for @Update annotation.
 */
class UpdateAnnotationDescriber implements ModelRegistryAwareInterface, RouteDescriberInterface
{
    use ModelRegistryAwareTrait;
    use RouteDescriberTrait;

    public function __construct(
        private AnnotationReader $annotationReader,
        private FormFactoryInterface $formFactory
    ) {
    }

    public function describe(OA\OpenApi $api, Route $route, ReflectionMethod $reflectionMethod): void
    {
        $updates = $this->getAnnotations($reflectionMethod);
        if (empty($updates)) {
            return;
        }

        foreach ($this->getOperations($api, $route) as $operation) {
            $this->addUpdatesToOperation($operation, $updates);
        }
    }

    /**
     * @param Update[] $payloads
     */
    private function addUpdatesToOperation(OA\Operation $operation, array $payloads): void
    {
        foreach ($payloads as $payload) {
            $form = $this->formFactory->create($payload->getOptions()['type']);
            $resolvedType = get_class($form->getConfig()->getType()->getInnerType());

            $parameter = Util::getOperationParameter($operation, 'body', 'body');
            $parameter->description = '(Partial) representation of ' . $resolvedType . ' structure. Primary key is optional, '
                . 'associations may be supplied via primary keys and optional fields can be left out.';
            $parameter->required = true;

            /** @var OA\Schema $schema */
            $schema = Util::getChild($parameter, OA\Schema::class);
            $schema->ref = $this->getModelReference($resolvedType);
        }
    }

    private function getModelReference(string $modelClass): string
    {
        return $this->modelRegistry->register(
            new Model(
                new Type(
                    Type::BUILTIN_TYPE_OBJECT,
                    false,
                    $modelClass
                )
            )
        );
    }

    /**
     * @return Update[]
     */
    private function getAnnotations(ReflectionMethod $method): array
    {
        $annotations = $this->annotationReader->getMethodAnnotations($method);
        $annotations = array_filter($annotations, static fn ($value) => $value instanceof Update);

        $attributes = array_map(
            static fn (ReflectionAttribute $attribute): object => $attribute->newInstance(),
            $method->getAttributes(Update::class, ReflectionAttribute::IS_INSTANCEOF)
        );

        return array_merge($attributes, $annotations);
    }
}
