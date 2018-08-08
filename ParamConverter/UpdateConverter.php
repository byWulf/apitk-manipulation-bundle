<?php

declare(strict_types=1);

namespace Shopping\ApiTKManipulationBundle\ParamConverter;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Shopping\ApiTKCommonBundle\Exception\ValidationException;
use Shopping\ApiTKManipulationBundle\Annotation\Update;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UpdateConverter.
 *
 * Handles POST, PUT and PATCH requests for a given entity and form type.
 * For PUT & PATCH, it will fetch the requested entity from doctrine, create a form and validate it.
 * For POST, it will create a new entity instance from doctrine, create a form and validate it.
 *
 * Objects without validation errors will be updated/persisted to the DB automatically.
 *
 * @package Shopping\ApiTKManipulationBundle\ParamConverter
 */
class UpdateConverter implements ParamConverterInterface
{
    /**
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var string
     */
    private $entityClass;

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * UpdateConverter constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param ManagerRegistry|null $registry
     */
    public function __construct(FormFactoryInterface $formFactory, ManagerRegistry $registry = null)
    {
        $this->registry = $registry;
        $this->formFactory = $formFactory;
    }

    /**
     * Stores the object in the request.
     *
     * @param Request        $request
     * @param ParamConverter $configuration Contains the name, class and options of the object
     *
     * @throws EntityNotFoundException
     * @throws \InvalidArgumentException
     *
     * @return bool True if the object has been successfully set, else false
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $options = $configuration->getOptions();

        if (!isset($options['type'])) {
            throw new \InvalidArgumentException('You have to specify "type" option for the UpdateConverter.');
        }

        // already create the form to read the data_class from it
        $this->form = $this->formFactory->create($options['type'], null, ['csrf_protection' => false]);
        $this->entityClass = $this->form->getConfig()->getDataClass();

        if ($this->entityClass === null) {
            throw new \InvalidArgumentException(
                'You have to specify "data_class" option in "' . $options['type'] . '" for the UpdateConverter.'
            );
        }

        $om = $this->getManager($options['entityManager'] ?? null, $this->entityClass);

        if ($request->isMethod(Request::METHOD_POST)) {
            $entity = null;
        } else {
            $requestParamName = $options['requestParam'] ?? 'id';

            $entity = $this->getEntity(
                $this->entityClass,
                $om,
                $options['methodName'] ?? null,
                $request->attributes->get($requestParamName),
                $requestParamName
            );
        }

        $updatedEntity = $this->validateForm($this->form, $entity, $request);
        $om->persist($updatedEntity);
        $om->flush();

        $request->attributes->set($configuration->getName(), $updatedEntity);

        return true;
    }

    /**
     * @param string                                     $entityClass
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @param string|null                                $methodName
     * @param string|null                                $requestParam
     * @param string|null                                $requestParamName
     *
     * @throws EntityNotFoundException
     * @throws \InvalidArgumentException
     *
     * @return mixed Entity
     */
    private function getEntity(
        string $entityClass,
        ObjectManager $manager,
        string $methodName = null,
        string $requestParam = null,
        string $requestParamName = null
    ) {
        if ($requestParam === null) {
            throw new \InvalidArgumentException(
                sprintf(
                    '""%s" is missing from the Request attributes but is required for the UpdateConverter. '
                    . 'It defaults to "id" but may be changed via the "requestParam" option',
                    $requestParamName
                )
            );
        }

        $result = $this->findInRepository($entityClass, $manager, $methodName, $requestParam);

        if ($result === null) {
            throw new EntityNotFoundException(
                sprintf(
                    'Unable to find Entity of class %s with %s "%s"',
                    $entityClass,
                    $requestParamName,
                    $requestParam
                )
            );
        }

        return $result;
    }

    /**
     * @param FormInterface $form
     * @param Request       $request
     * @param mixed         $data
     *
     * @return mixed Updated entity
     */
    private function validateForm(FormInterface $form, $data, Request $request)
    {
        $form->setData($data);

        // clearMissing = true when method is put or post; patch allows partial bodies
        $form->submit($request->request->all(), !$request->isMethod(Request::METHOD_PATCH));

        if (!$form->isValid()) {
            throw new ValidationException((string) $form->getErrors());
        }

        return $form->getData();
    }

    /**
     * @param string                                     $entityClass
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @param string|null                                $methodName
     * @param string|null                                $requestParam
     *
     * @throws \InvalidArgumentException
     *
     * @return null|mixed Returns a matching entity or 0 if nothing has been found
     */
    private function findInRepository(
        string $entityClass,
        ObjectManager $manager,
        string $methodName = null,
        string $requestParam = null
    ) {
        $repository = $manager->getRepository($entityClass);

        if ($methodName === null) {
            $methodName = 'find';
        }

        return $repository->$methodName($requestParam);
    }

    /**
     * @param string|null $name
     * @param string      $entity
     *
     * @throws \InvalidArgumentException
     *
     * @return \Doctrine\Common\Persistence\ObjectManager|null
     */
    private function getManager(?string $name, string $entity)
    {
        if (null === $name) {
            $om = $this->registry->getManagerForClass($entity);
        } else {
            $om = $this->registry->getManager($name);
        }

        if ($om === null) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Entity Manager named "%s" does not exist. Please check the "entityManager" property of your Update annotation.',
                    $name
                )
            );
        }

        return $om;
    }

    /**
     * Checks if the object is supported.
     *
     * @param ParamConverter $configuration
     *
     * @return bool True if the object is supported, else false
     */
    public function supports(ParamConverter $configuration)
    {
        return $configuration instanceof Update && $this->registry instanceof ManagerRegistry;
    }
}
