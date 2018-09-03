# apitk-manipulation-bundle

API Toolkit Bundle to handle POST, PUT, PATCH and DELETE methods.

## Installation
Install the package via composer:
```
composer install check24/apitk-manipulation-bundle
```

## Usage
### Example Classes

See the `example`-Folder in this repository: https://github.com/alexdo/apitk-manipulation-bundle/tree/master/example

##### User entity

```php 
namespace MyApp\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class User
{
    private $id;

    /**
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @Assert\NotBlank()
     */
    private $fullname;
    
    // ...
} 
```

##### User FormType

```php
namespace MyApp\Form\Type;

use MyApp\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserV1Type extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class)
            ->add('email', EmailType::class)
            ->add('fullname', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
            ]
        );
    }
}
```

##### Controller

```php
namespace MyApp\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use MyApp\Entity\User;
use Shopping\ApiTKManipulationBundle\Annotation as Manipulation;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class UserV1Controller extends Controller
{
    /**
     * Create a new user.
     *
     * @Rest\Post("/v1/users/{id}")
     * @Manipulation\Update("user", type=UserV1Type::class)
     *
     * @SWG\Tag(name="User")
     *
     * @param User $user
     *
     * @return Response
     */
    public function postUserV1(User $user): Response
    {
        return new Response('', 200);
    }

    /**
     * Update all properties of a given user.
     *
     * @Rest\Put("/v1/users/{id}")
     * @Manipulation\Update("user", type=UserV1Type::class)
     *
     * @SWG\Tag(name="User")
     *
     * @param User $user
     *
     * @return Response
     */
    public function putUserV1(User $user): Response
    {
        return new Response('', 200);
    }

    /**
     * Partially update a users's properties.
     *
     * @Rest\Patch("/v1/users/{id}")
     * @Manipulation\Update("user", type=UserV1Type::class)
     *
     * @SWG\Tag(name="User")
     *
     * @param User $user
     *
     * @return Response
     */
    public function patchUserV1(User $user): Response
    {
        return new Response('', 200);
    }

    /**
     * Remove a user.
     *
     * @Rest\Delete("/v1/user/{id}")
     *
     * @Manipulation\Delete("id", entity=User::class)
     *
     * @SWG\Tag(name="User")
     *
     * @return Response
     */
    public function deleteUserV1(Response $response): Response
    {
        return $response;
    }
}
```


#### Updating Entities (POST, PUT, PATCH)

Updating entites works by mapping request data to a form, validating it and then persisting the form's data
to the Database via Doctrine.

You'll need:
* Doctrine Entity
* Form Type with data_class
* Controller Action


```
use Shopping\ApiTKManipulationBundle\Annotation as Manipulation;

/**
 * Partially update a users's properties.
 *
 * @Rest\Patch("/v1/users/{id}")
 * @Manipulation\Update("user", type=UserV1Type::class)
 *
 * @SWG\Tag(name="User")
 *
 * @param User $user
 *
 * @return Response
 */
public function patchUserV1(User $user): Response
{
    return new Response('', 200);
}

```

This will also auto-generate appropriate Swagger parameters. Usage of FOSRest bundle is optional. Controller methods
look the same for POST and PUT.

The `$user` parameter is an already updated version of the requested User. You can do anything you want with it:
Serialization, JSON encode, etc.


Internally, the `Update` annotation fetches the `default` EntityManager, gets the Repository and executes `find($id)`
on it. You can customize this in three ways:

1. If you'd like to use a **different ORM connection** than `default`, supply it via `entityManager`:

   ```
   @Manipulation\Update("user", type=UserV1Type::class, entityManager="custom")
   ```
   
   This will cause the annotation to fetch the repository and entity from the "custom" EntityManager.

2. If you'd like to use a **different Repository method** than `find`, use the `methodName` option:

   ```
   @Manipulation\Update("user", type=UserV1Type::class, methodName="findById")
   ```

3. If your **path component is something different** than `id`, eg. `email`, you can supply the parameter 
    to use via the `requestParam` option:
    
   ```
   @Manipulation\Update("user", type=UserV1Type::class, requestParam="email", methodName="findByEmail")
   ```
 


#### Removing Entities (DELETE)

To remove an entity, add an action to your controller that uses the `Delete`-Annotation.

```
use Shopping\ApiTKManipulationBundle\Annotation as Manipulation;

/**
 * Remove a user.
 *
 * @Rest\Delete("/v1/user/{id}")
 *
 * @Manipulation\Delete("id", entity=User::class)
 *
 * @SWG\Tag(name="User")
 *
 * @return Response
 */
public function deleteUserV1(Response $response): Response
{
    return $response;
}
```

`@Manipulation\Delete("id", entity=User::class)`
* `"id"` is the name of the path-component to get the ID from
* `entity=User::class` is the type of the entity to be removed

The `$response` parameter is a HTTP 204-Response (No Content). You can just return it, or ignore 
it and build one of your own. 

`Delete` will, by default, perform a hard delete by executing `find($id)` on `UserRepository` and then 
removing the Entity via `ObjectManager::remove`.

As with `Update`, you can customize this behaviour:

1. If you'd like to **perform a soft delete or other things** instead, use the `methodName` option:
 
   ```
   @Manipulation\Delete("id", entity=User::class, methodName="deleteById")
   ```
   
   This will cause the Annotation to call `DeleteRepository::deleteById(DeletionService $deletionService)`. 
   The `DeletionService` allows you to access all request parameters. Perform anything you want to mark your
   Entity as deleted, eg. setting a deleted-Property from 0 to 1 and then flushing the EntityManager.

2. If you'd like to use a **different ORM connection** than `default`, supply it via `entityManager`:

   ```
   @Manipulation\Delete("id", entity=User::class, entityManager="custom")
   ```
   
   This will cause the annotation to operate against the "custom" EntityManager.

