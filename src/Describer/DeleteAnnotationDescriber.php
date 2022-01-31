<?php

declare(strict_types=1);

namespace Shopping\ApiTKManipulationBundle\Describer;

use Doctrine\Common\Annotations\AnnotationReader;
use Nelmio\ApiDocBundle\OpenApiPhp\Util;
use Nelmio\ApiDocBundle\RouteDescriber\RouteDescriberInterface;
use OpenApi\Annotations as OA;
use ReflectionAttribute;
use ReflectionMethod;
use Shopping\ApiTKCommonBundle\Describer\RouteDescriberTrait;
use Shopping\ApiTKManipulationBundle\Annotation\Delete;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

/**
 * Provides automatic Parameter OpenApi annotations for actions that use the
 * param converter for Delete annotation.
 */
class DeleteAnnotationDescriber implements RouteDescriberInterface
{
    use RouteDescriberTrait;

    public function __construct(
        private AnnotationReader $annotationReader
    ) {
    }

    public function describe(OA\OpenApi $api, Route $route, ReflectionMethod $reflectionMethod): void
    {
        $deletes = $this->getAnnotations($reflectionMethod);
        if (empty($deletes)) {
            return;
        }

        foreach ($this->getOperations($api, $route) as $operation) {
            $this->addDeletesToOperation($operation, $deletes, $route);
        }
    }

    /**
     * @param Delete[] $deletes
     */
    private function addDeletesToOperation(OA\Operation $operation, array $deletes, Route $route): void
    {
        $routePlaceholders = [];
        preg_match_all('/{([^}]+)}/', $route->getPath(), $routePlaceholders);
        $routePlaceholders = $routePlaceholders[1] ?? [];

        foreach ($deletes as $delete) {
            if (in_array($delete->getName(), $routePlaceholders, true)) {
                $parameter = Util::getOperationParameter($operation, $delete->getName(), 'path');
            } else {
                $parameter = Util::getOperationParameter($operation, $delete->getName(), 'query');
            }

            $parameter->description = 'Delete entity with this ' . $delete->getName() . '.';
            $parameter->required = true;

            /** @var OA\Schema $schema */
            $schema = Util::getChild($parameter, OA\Schema::class);
            $schema->type = 'string';

            /** @var OA\Response $response */
            $response = Util::getCollectionItem($operation, OA\Response::class, ['response' => Response::HTTP_NO_CONTENT]);
            $response->description = 'Returns HTTP 204 on success';
        }
    }

    /**
     * @return Delete[]
     */
    private function getAnnotations(ReflectionMethod $method): array
    {
        $annotations = $this->annotationReader->getMethodAnnotations($method);
        $annotations = array_filter($annotations, static fn ($value) => $value instanceof Delete);

        $attributes = array_map(
            static fn (ReflectionAttribute $attribute): object => $attribute->newInstance(),
            $method->getAttributes(Delete::class, ReflectionAttribute::IS_INSTANCEOF)
        );

        return array_merge($attributes, $annotations);
    }
}
