# apitk-update-bundle

API Toolkit Bundle to handle POST, PUT, PATCH and DELETE methods.

## Installation
Add this repository to your `composer.json` until it is available at packagist:
```
{
    "repositories": [{
            "type": "vcs",
            "url": "git@github.com:alexdo/apitk-update-bundle.git"
        }
    ]
}
```

After that, install the package via composer:
```
composer install shopping/apitk-update-bundle:dev-master
```

## Usage
#### Updating Entities (POST, PUT, PATCH)

Updating entites works by mapping request data to a form and then persisting the form's data
to the Database via Doctrine.

You'll need:
* Doctrine Entity
* Form Type with data_class
* Controller Action


```
use Shopping\ApiTKUpdateBundle\Annotation as Update;

/**
 * Partially update a deal's properties.
 *
 * @Rest\Patch("/v1/deals/{id}")
 * @Update\Payload("deal", type=DealV1Type::class)
 * @Dto\View(dtoMapper="App\DtoMapper\DealV1Mapper")
 *
 * @SWG\Tag(name="Deal")
 *
 * @param Deal $deal
 *
 * @return Deal
 */
public function patchDealV1(Deal $deal): Deal
{
    return $deal;
}

```

This will also auto-generate appropriate Swagger parameters.

apitk-update-bundle plays nicely with all other apitk bundles. The example above uses 
the `Dto\View`-Annotation from apitk-dto-mapper-bundle to transform our updated entity
in a DTO and return it as e.g. JSON. 
 

#### Removing Entities (DELETE)

To remove an entity, add an action to your controller that uses the `Delete`-Annotation.

```
use Shopping\ApiTKUpdateBundle\Annotation as Update;

/**
 * Remove a deal.
 *
 * @Rest\Delete("/v1/deals/{id}")
 *
 * @Update\Delete("id", entity=Deal::class)
 *
 * @SWG\Tag(name="Deal")
 *
 * @return Response
 */
public function deleteDealV1(Response $response): Response
{
    return $response;
}
```

`@Update\Delete("id", entity=Deal::class)`
* `"id"` is the name of the path-component to get the ID from
* `entity=Deal::class` is the type of the entity to be removed

Next, make sure your `DealRepository` implements `ApiTKDeletableRepositoryInterface`
and add the appropriate method:

```
use Shopping\ApiTKUpdateBundle\Repository\ApiTKDeletableRepositoryInterface;

class DealRepository extends EntityRepository implements ApiTKDeletableRepositoryInterface
{
    /**
     * Delete the entity for a given parameter.
     * You may delete the entity from the database here, perform a soft delete
     * or other things to mark your entity as deleted.
     *
     * @param string $idParameter
     *
     * The param converter is able to handle these exceptions, so use them appropriately:
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\EntityNotFoundException
     *
     * @return bool deletion status
     */
    public function deleteByRequest(string $idParameter): bool
    {
        $em = $this->getEntityManager();

        /** @var Deal|null $deal */
        $deal = $this->find($idParameter);

        if ($deal === null) {
            throw new EntityNotFoundException();
        }

        $em->remove($deal);
        $em->flush();

        return true;
    }

}
```
 

`string $idParameter` is the value of the path component you supplied earlier
in the controller annotation.

`deleteByRequest` takes care of deleting a given entity. Usually you need to
fetch your entity and delete it, but you can also set a deleted property to true
for soft deletion or perform any other operation marking your entity as deleted. 
