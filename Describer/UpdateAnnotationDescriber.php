<?php

declare(strict_types=1);

namespace Shopping\ApiTKManipulationBundle\Describer;

use Doctrine\Common\Annotations\Reader;
use EXSyst\Component\Swagger\Operation;
use EXSyst\Component\Swagger\Parameter;
use EXSyst\Component\Swagger\Path;
use Nelmio\ApiDocBundle\Describer\ModelRegistryAwareInterface;
use Nelmio\ApiDocBundle\Describer\ModelRegistryAwareTrait;
use Nelmio\ApiDocBundle\Model\Model;
use ReflectionMethod;
use Shopping\ApiTKCommonBundle\Describer\AbstractDescriber;
use Shopping\ApiTKCommonBundle\Util\ControllerReflector;
use Shopping\ApiTKManipulationBundle\Annotation\Update;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class UpdateAnnotationDescriber.
 *
 * Provides automatic @Parameter swagger annotations for actions that use the
 * param converter for @Update annotation.
 *
 * @package Shopping\ApiTKManipulationBundle\Describer
 */
class UpdateAnnotationDescriber extends AbstractDescriber implements ModelRegistryAwareInterface
{
    use ModelRegistryAwareTrait;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @param RouteCollection      $routeCollection
     * @param ControllerReflector  $controllerReflector
     * @param Reader               $reader
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(
        RouteCollection $routeCollection,
        ControllerReflector $controllerReflector,
        Reader $reader,
        FormFactoryInterface $formFactory
    ) {
        parent::__construct($routeCollection, $controllerReflector, $reader);
        $this->formFactory = $formFactory;
    }

    /**
     * @param Operation        $operation
     * @param ReflectionMethod $classMethod
     * @param Path             $path
     * @param string           $method
     */
    protected function handleOperation(
        Operation $operation,
        ReflectionMethod $classMethod,
        Path $path,
        string $method
    ): void {
        $methodAnnotations = $this->reader->getMethodAnnotations($classMethod);

        /** @var Update[] $payloads */
        $payloads = array_filter($methodAnnotations, function ($annotation) { return $annotation instanceof Update; });
        $this->addUpdatesToOperation($operation, $payloads);
    }

    /**
     * @param Operation $operation
     * @param Update[]  $payloads
     */
    private function addUpdatesToOperation(Operation $operation, array $payloads): void
    {
        foreach ($payloads as $payload) {
            $form = $this->formFactory->create($payload->getOptions()['type']);
            $resolvedType = get_class($form->getConfig()->getType()->getInnerType());

            $parameter = new Parameter(
                [
                    'name' => 'body',
                    'in' => 'body',
                    'description' => '(Partial) representation of ' . $resolvedType . ' structure. Primary key is optional, '
                        . 'associations may be supplied via primary keys and optional fields can be left out.',
                    'required' => true,
                    'schema' => [
                        '$ref' => $this->getModelReference($resolvedType),
                    ],
                ]
            );

            $operation->getParameters()->add($parameter);
        }
    }

    /**
     * @param string $modelClass
     *
     * @return string
     */
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
}
