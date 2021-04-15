<?php

declare(strict_types=1);

namespace App\Controller\Rest;

use App\Controller\Rest\Traits\HateoasResponseTrait;
use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @OA\Tag(name="Users")
 * @Security(name="Bearer")
 */
class ApiUserController extends ApiBaseController
{
    use HateoasResponseTrait;

    public function __construct(private UserRepositoryInterface $userRepository)
    {
    }

    /**
     * @Rest\Get("/api/v1/users", name="api_get_users")
     *
     * @OA\Parameter(
     *     in="query",
     *     name="page",
     *     description="Page from which to start listing users.",
     *     required=true,
     *     @OA\Schema(type="integer", default="1")
     * )
     * @OA\Parameter(
     *     in="query",
     *     name="limit",
     *     description="How many items to return.",
     *     required=true,
     *     @OA\Schema(type="integer", default="10")
     * )
     * @OA\Parameter(
     *     in="query",
     *     name="orderBy[updatedAt]",
     *     description="Order by last updated",
     *     required=false,
     *     @OA\Schema(
     *         type="string",
     *         enum={"ASC", "DESC"},
     *         default="DESC"
     *     )
     * )
     * @OA\Parameter(
     *     in="query",
     *     name="serializerGroups[]",
     *     description="Custom serializer groups array",
     *     required=false,
     *     style="form",
     *     @OA\Schema(
     *         type="array",
     *         @OA\Items(
     *             type="string",
     *             enum={
     *                 "user",
     *                 "user.articles",
     *                 "article"
     *             }
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=Response::HTTP_OK,
     *     description="Success",
     *     @OA\Schema(
     *         type="array",
     *         @OA\Items(
     *             ref=@Model(type=User::class, groups={"user"})
     *         )
     *     )
     * )
     */
    public function index(Request $request): Response
    {
        return $this->handleCollectionRequest($this->userRepository, $request, [
            'serializerGroups' => [
                'user',
            ],
        ]);
    }

    /**
     * @Rest\Get("/api/v1/user/{id}", name="api_get_user")
     * @Security(name="Bearer")
     *
     * @OA\Parameter(
     *     in="query",
     *     name="serializerGroups[]",
     *     description="Custom serializer groups array",
     *     required=false,
     *     style="form",
     *     @OA\Schema(
     *         type="array",
     *         @OA\Items(
     *             type="string",
     *             enum={
     *                 "user",
     *                 "user.articles",
     *                 "article"
     *             }
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=Response::HTTP_OK,
     *     description="Success",
     *     @OA\Schema(
     *         type="object",
     *         ref=@Model(
     *             type=User::class,
     *             groups={"user"}
     *         )
     *     )
     * )
     */
    public function detail(Request $request, int $id): Response
    {
        $params = (new OptionsResolver())
            ->setDefaults([
                'serializerGroups' => [
                    'user',
                ],
            ])
            ->resolve($request->query->all());

        $entity = $this->userRepository->find($id);

        return $this->serializeResponse($entity, $params['serializerGroups']);
    }

    /**
     * @Rest\Get("/api/v1/users/me", name="api_get_users_me")
     *
     * @OA\Parameter(
     *     in="query",
     *     name="serializerGroups[]",
     *     description="Custom serializer groups array",
     *     required=false,
     *     style="form",
     *     @OA\Schema(
     *         type="array",
     *         @OA\Items(
     *             type="string",
     *             enum={
     *                 "user",
     *                 "user.articles",
     *                 "article"
     *             }
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=Response::HTTP_OK,
     *     description="Success",
     *     @OA\Schema(
     *         type="object",
     *         ref=@Model(type=User::class, groups={"user"})
     *     )
     * )
     *
     * @throws \Exception
     */
    public function me(Request $request): Response
    {
        $params = $request->query->all();
        $serializerGroups = $params['serializerGroups'] ?? ['user'];
        $data = $this->getLoggedUser();

        return $this->serializeResponse($data, $serializerGroups);
    }
}
