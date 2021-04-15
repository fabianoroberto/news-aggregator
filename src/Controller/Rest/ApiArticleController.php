<?php

declare(strict_types=1);

namespace App\Controller\Rest;

use App\Controller\Rest\Traits\HateoasResponseTrait;
use App\Entity\Article;
use App\Repository\ArticleRepositoryInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ApiArticleController
 *
 * @OA\Tag(name="Articles")
 */
class ApiArticleController extends AbstractFOSRestController
{
    use HateoasResponseTrait;

    private ArticleRepositoryInterface $articleRepository;

    public function __construct(ArticleRepositoryInterface $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    /**
     * @Rest\Get("/api/v1/articles", name="api_get_articles")
     *
     * @OA\Parameter(
     *     in="query",
     *     name="page",
     *     description="Page from which to start listing articles.",
     *     required=true,
     *     @OA\Schema(type="integer", default="1")
     * )
     * @OA\Parameter(
     *     in="query",
     *     name="limit",
     *     description="How many items to return",
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
     *                 "article",
     *                 "article.user",
     *                 "user",
     *                 "article.comments",
     *                 "comments"
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
     *             ref=@Model(type=Article::class, groups={"article"})
     *         )
     *     )
     * )
     */
    public function index(Request $request): Response
    {
        return $this->handleCollectionRequest($this->articleRepository, $request, [
            'serializerGroups' => [
                'article',
            ],
        ]);
    }

    /**
     * @Rest\Get("/api/v1/article/{id}", name="api_get_article")
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
     *                 "article",
     *                 "article.user",
     *                 "user",
     *                 "article.comments",
     *                 "comments"
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
     *             type=Article::class,
     *             groups={"article"}
     *         )
     *     )
     * )
     */
    public function detail(Request $request, int $id): Response
    {
        $params = (new OptionsResolver())
            ->setDefaults([
                'serializerGroups' => [
                    'article',
                ],
            ])
            ->resolve($request->query->all());

        $entity = $this->articleRepository->find($id);

        return $this->serializeResponse($entity, $params['serializerGroups']);
    }
}
