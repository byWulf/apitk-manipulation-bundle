<?php

declare(strict_types=1);

namespace Shopping\ApiTKManipulationBundle\ParamConverter;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;
use RuntimeException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Shopping\ApiTKCommonBundle\Exception\ValidationException;
use Shopping\ApiTKCommonBundle\ParamConverter\ContextAwareParamConverterTrait;
use Shopping\ApiTKCommonBundle\ParamConverter\EntityAwareParamConverterTrait;
use Shopping\ApiTKCommonBundle\ParamConverter\RequestParamAwareParamConverterTrait;
use Shopping\ApiTKManipulationBundle\Annotation\Update;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handles POST, PUT and PATCH requests for a given entity and form type.
 * For PUT & PATCH, it will fetch the requested entity from doctrine, create a form and validate it.
 * For POST, it will create a new entity instance from doctrine, create a form and validate it.
 *
 * Objects without validation errors will be updated/persisted to the DB automatically.
 */
class UpdateConverter implements ParamConverterInterface
{
    use ContextAwareParamConverterTrait;
    use EntityAwareParamConverterTrait;
    use RequestParamAwareParamConverterTrait;

    public function __construct(
        private FormFactoryInterface $formFactory,
        ManagerRegistry $registry,
        private ContainerBagInterface $containerBag
    ) {
        $this->registry = $registry;
    }

    /**
     * Stores the object in the request.
     *
     * @param ParamConverter $configuration Contains the name, class and options of the object
     *
     * @throws EntityNotFoundException
     * @throws InvalidArgumentException
     *
     * @return bool True if the object has been successfully set, else false
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $this->initialize($request, $configuration);

        if ($this->getOption('type') === null) {
            throw new InvalidArgumentException('You have to specify "type" option for the UpdateConverter.');
        }

        $csrfOptions = [];
        if ($this->containerBag->has('form.type_extension.csrf.enabled') && $this->containerBag->get('form.type_extension.csrf.enabled') === true) {
            $csrfOptions = ['csrf_protection' => false];
        }

        // already create the form to read the data_class from it
        $form = $this->formFactory->create($this->getOption('type'), null, $csrfOptions);
        // save it back to our parameterbag so EntityAwareParamConverterTrait knows what to do
        $this->getOptions()->set('entity', $form->getConfig()->getDataClass());

        if ($this->getEntity() === null) {
            throw new InvalidArgumentException(
                'You have to specify "data_class" option in "' . $this->getOption('type') . '" for the UpdateConverter.'
            );
        }

        if ($request->isMethod(Request::METHOD_POST)) {
            $entity = null;
        } else {
            $entity = $this->fetchEntity();
        }

        $updatedEntity = $this->validateForm($form, $entity, $request);

        $om = $this->getManager();
        if ($om === null) {
            throw new RuntimeException('Unable to perform update as EntityManager is null');
        }

        $om->persist($updatedEntity);
        $om->flush();

        $request->attributes->set($configuration->getName(), $updatedEntity);

        return true;
    }

    /**
     * @throws EntityNotFoundException
     *
     * @return mixed Entity
     */
    private function fetchEntity(): mixed
    {
        if ($this->getRequestParamValue() === null) {
            throw new InvalidArgumentException(
                sprintf(
                    '""%s" is missing from the Request attributes but is required for the UpdateConverter. '
                    . 'It defaults to "id" but may be changed via the "requestParam" option',
                    $this->getRequestParamName()
                )
            );
        }

        $result = $this->findInRepository();

        if ($result === null) {
            throw new EntityNotFoundException(
                sprintf(
                    'Unable to find Entity of class %s with %s "%s"',
                    $this->getEntity(),
                    $this->getRequestParamName(),
                    $this->getRequestParamValue()
                )
            );
        }

        return $result;
    }

    /**
     * @param FormInterface<mixed> $form
     *
     * @return mixed Updated entity
     */
    private function validateForm(FormInterface $form, mixed $data, Request $request): mixed
    {
        $form->setData($data);

        // clearMissing = true when method is put or post; patch allows partial bodies
        $form->submit($request->request->all(), !$request->isMethod(Request::METHOD_PATCH));

        if (!$form->isValid()) {
            throw new ValidationException((string) $form->getErrors(true, true));
        }

        return $form->getData();
    }

    /**
     * @return mixed Returns a matching entity or null if nothing has been found
     */
    private function findInRepository(): mixed
    {
        $om = $this->getManager();
        if ($om === null) {
            throw new RuntimeException('Unable to read entity to update as EntityManager is null');
        }

        /** @var class-string $entityClassName */
        $entityClassName = $this->getEntity();

        $repository = $om->getRepository($entityClassName);
        $methodName = $this->getRepositoryMethodName('find');

        return $repository->$methodName($this->getRequestParamValue());
    }

    /**
     * Checks if the object is supported.
     *
     * @return bool True if the object is supported, else false
     */
    public function supports(ParamConverter $configuration): bool
    {
        return $configuration instanceof Update && $this->registry instanceof ManagerRegistry;
    }
}
