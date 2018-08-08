<?php

declare(strict_types=1);

namespace Shopping\ApiTKUpdateBundle\Describer;

use Doctrine\Common\Annotations\Reader;
use EXSyst\Component\Swagger\Operation;
use EXSyst\Component\Swagger\Parameter;
use Nelmio\ApiDocBundle\Describer\ModelRegistryAwareInterface;
use Nelmio\ApiDocBundle\Describer\ModelRegistryAwareTrait;
use Nelmio\ApiDocBundle\Model\Model;
use Shopping\ApiTKCommonBundle\Describer\AbstractDescriber;
use Shopping\ApiTKCommonBundle\Util\ControllerReflector;
use Shopping\ApiTKUpdateBundle\Annotation\Payload;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class PayloadAnnotationDescriber.
 *
 * Provides automatic @Parameter swagger annotations for actions that use the
 * param converter for @Payload annotation.
 *
 * @package Shopping\ApiTKUpdateBundle\Describer
 *
 * @author Alexander Dormann <alexander.dormann@check24.de>
 */
class PayloadAnnotationDescriber extends AbstractDescriber implements ModelRegistryAwareInterface
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
     * @param Operation         $operation
     * @param \ReflectionMethod $classMethod
     */
    protected function handleOperation(Operation $operation, \ReflectionMethod $classMethod): void
    {
        $methodAnnotations = $this->reader->getMethodAnnotations($classMethod);

        /** @var Payload[] $payloads */
        $payloads = array_filter($methodAnnotations, function ($annotation) { return $annotation instanceof Payload; });
        $this->addPayloadsToOperation($operation, $payloads);
    }

    /**
     * @param Operation $operation
     * @param Payload[] $payloads
     */
    private function addPayloadsToOperation(Operation $operation, array $payloads): void
    {
        foreach ($payloads as $payload) {
            $form = $this->formFactory->create($payload->getOptions()['type']);
            $entity = $form->getConfig()->getDataClass();

            $parameter = new Parameter(
                [
                    'name' => 'body',
                    'in' => 'body',
                    'description' => '(Partial) representation of ' . $entity . ' structure. Primary key is optional, '
                        . 'associations may be supplied via primary keys and optional fields can be left out.',
                    'required' => true,
                    'schema' => [
                        '$ref' => $this->getModelReference($entity),
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
