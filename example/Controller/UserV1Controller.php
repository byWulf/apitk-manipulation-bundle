<?php

declare(strict_types=1);

namespace MyApp\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use MyApp\Entity\User;
use MyApp\Form\Type\UserV1Type;
use OpenApi\Annotations as OA;
use Shopping\ApiTKManipulationBundle\Annotation as Manipulation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(name="User")
 */
class UserV1Controller extends Controller
{
    /**
     * Create a new user.
     */
    #[Rest\Post('/v1/users/{id}')]
    #[Manipulation\Update('user', type: UserV1Type::class)]
    public function postUserV1(User $user): Response
    {
        return new Response('', 200);
    }

    /**
     * Update all properties of a given user.
     */
    #[Rest\Put('/v1/users/{id}')]
    #[Manipulation\Update('user', type: UserV1Type::class)]
    public function putUserV1(User $user): Response
    {
        return new Response('', 200);
    }

    /**
     * Partially update a users's properties.
     */
    #[Rest\Patch('/v1/users/{id}')]
    #[Manipulation\Update('user', type: UserV1Type::class)]
    public function patchUserV1(User $user): Response
    {
        return new Response('', 200);
    }

    /**
     * Remove a user.
     */
    #[Rest\Delete('/v1/user/{id}')]
    #[Manipulation\Delete('id', entity: User::class)]
    public function deleteUserV1(Response $response): Response
    {
        return $response;
    }
}
