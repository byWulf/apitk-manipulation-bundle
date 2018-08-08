<?php

namespace Shopping\ApiTKUpdateBundle\Describer;

use EXSyst\Component\Swagger\Operation;
use EXSyst\Component\Swagger\Parameter;
use EXSyst\Component\Swagger\Response;
use Shopping\ApiTKCommonBundle\Describer\AbstractDescriber;
use Shopping\ApiTKUpdateBundle\Annotation\Delete;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DeleteAnnotationDescriber.
 *
 * Provides automatic Parameter swagger annotations for actions that use the
 * param converter for Delete annotation.
 *
 * @package Shopping\ApiTKUpdateBundle\Describer
 *
 * @author Alexander Dormann <alexander.dormann@check24.de>
 */
class DeleteAnnotationDescriber extends AbstractDescriber
{
    /**
     * @param Operation         $operation
     * @param \ReflectionMethod $classMethod
     */
    protected function handleOperation(Operation $operation, \ReflectionMethod $classMethod): void
    {
        $methodAnnotations = $this->reader->getMethodAnnotations($classMethod);

        /** @var Delete[] $deletes */
        $deletes = array_filter($methodAnnotations, function ($annotation) { return $annotation instanceof Delete; });
        /** @var Route[] $routes */
        $routes = array_filter($methodAnnotations, function ($annotation) { return $annotation instanceof Route; });
        $this->addDeletesToOperation($operation, $deletes, $routes);

        $operation->getResponses()->set(
            \Symfony\Component\HttpFoundation\Response::HTTP_NO_CONTENT,
            new Response([
                'description' => 'Returns HTTP 204 on success',
            ])
        );
    }

    /**
     * @param Operation $operation
     * @param Delete[]  $deletes
     * @param Route[]   $routes
     */
    private function addDeletesToOperation(Operation $operation, array $deletes, array $routes): void
    {
        $routePlaceholders = [];
        foreach ($routes as $route) {
            $matches = [];
            preg_match_all('/{([^}]+)}/', $route->getPath(), $matches);
            $routePlaceholders = array_merge($routePlaceholders, $matches[1]);
        }

        foreach ($deletes as $delete) {
            if (in_array($delete->getName(), $routePlaceholders)) {
                $parameter = new Parameter(
                    [
                        'name' => $delete->getName(),
                        'in' => 'path',
                        'type' => 'string',
                        'required' => true,
                        'description' => 'Delete entity with this ' . $delete->getName() . '.',
                    ]
                );
            } else {
                $parameter = new Parameter(
                    [
                        'name' => $delete->getName(),
                        'in' => 'query',
                        'type' => 'string',
                        'required' => true,
                        'description' => 'Delete entity with this ' . $delete->getName() . '.',
                    ]
                );
            }

            $operation->getParameters()->add($parameter);
        }
    }
}
