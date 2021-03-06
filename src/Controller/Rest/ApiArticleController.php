<?php

declare(strict_types=1);

namespace App\Controller\Rest;

use App\Controller\Rest\Traits\ErrorResponseTrait;
use App\Controller\Rest\Traits\HateoasResponseTrait;
use App\Dto\Request\ArticleCreateRequest;
use App\Dto\Request\ArticleUpdateRequest;
use App\Entity\Article;
use App\Repository\ArticleRepositoryInterface;
use App\Service\ArticleService;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Webmozart\Assert\Assert;

/**
 * Class ApiArticleController
 *
 * @OA\Tag(name="Articles")
 */
class ApiArticleController extends ApiBaseController
{
    use ErrorResponseTrait;
    use HateoasResponseTrait;

    public function __construct(
        private ArticleRepositoryInterface $articleRepository,
        private ArticleService $articleService,
    ) {
    }

    /**
     * @Rest\Get("/v1/public/articles", name="api_get_articles")
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
     * @Rest\Get("/v1/public/articles/{id}", name="api_get_article")
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
    public function detail(Request $request, Article $article): Response
    {
        $params = (new OptionsResolver())
            ->setDefaults([
                'serializerGroups' => [
                    'article',
                ],
            ])
            ->resolve($request->query->all());

        //$article = $this->articleRepository->find($id);

        return $this->serializeResponse($article, $params['serializerGroups']);
    }

    /**
     * @Rest\Post("/v1/articles", name="api_post_article")
     * @ParamConverter("articleCreateRequest", converter="fos_rest.request_body")
     *
     * @OA\RequestBody(
     *     @Model(type=ArticleCreateRequest::class),
     * ),
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
     *
     * @throws \Exception
     */
    public function create(
        ArticleCreateRequest $articleCreateRequest,
        Request $request,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        if (\count($validationErrors) > 0) {
            $view = $this->constraintViolationView($validationErrors);

            return $this->handleView($view);
        }

        $params = (new OptionsResolver())
            ->setDefaults([
                'serializerGroups' => [
                    'article',
                ],
            ])
            ->resolve($request->query->all());

        $user = $this->getLoggedUser();

        $article = $this->articleService->store($articleCreateRequest, $user);

        return $this->serializeResponse($article, $params['serializerGroups']);
    }

    /**
     * @Rest\Put("/v1/articles/{id}", name="api_put_article")
     * @ParamConverter("articleUpdateRequest", converter="fos_rest.request_body")
     *
     * @OA\RequestBody(
     *     @Model(type=ArticleCreateRequest::class),
     * ),
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
     *
     * @throws \Exception
     */
    public function update(
        ArticleUpdateRequest $articleUpdateRequest,
        Article $article,
        Request $request,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        if (\count($validationErrors) > 0) {
            $view = $this->constraintViolationView($validationErrors);

            return $this->handleView($view);
        }

        $user = $this->getLoggedUser();

        if (!$user->isAdmin()) {
            Assert::eq($article->getAuthor()->getUuid(), $user->getUuid(), 'You can edit only YOURS articles');
        }

        $params = (new OptionsResolver())
            ->setDefaults([
                'serializerGroups' => [
                    'article',
                ],
            ])
            ->resolve($request->query->all());

        $article = $this->articleService->update($articleUpdateRequest, $article);

        return $this->serializeResponse($article, $params['serializerGroups']);
    }

    /**
     * @Rest\Post("/v1/articles/{id}/image", name="api_post_article_image")
     *
     * @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="multipart/form-data",
     *         @OA\Schema(
     *             @OA\Property(
     *                 description="File to upload",
     *                 property="cover",
     *                 type="string",
     *                 format="file",
     *             ),
     *             required={"file"}
     *         )
     *     )
     * ),
     * @OA\Parameter(
     *     description="ID of article to update",
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(
     *         type="string",
     *         format="uuid"
     *     ),
     * )
     *
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
    public function saveImage(Request $request, Article $article): Response
    {
        $image = $request->files->get('cover');

        $user = $this->getLoggedUser();

        if (!$user->isAdmin()) {
            Assert::eq($article->getAuthor()->getUuid(), $user->getUuid(), 'You can edit only YOURS articles');
        }

        $params = (new OptionsResolver())
            ->setDefaults([
                'serializerGroups' => [
                    'article',
                ],
            ])
            ->resolve($request->query->all());

        $article = $this->articleService->saveImage($article, $image);

        return $this->serializeResponse($article, $params['serializerGroups']);
    }

    /**
     * @Rest\Delete("/v1/articles/{id}", name="api_delete_article")
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
     *     description="Success"
     * )
     *
     * @throws \Exception
     */
    public function delete(Article $article): JsonResponse
    {
        $user = $this->getLoggedUser();

        if (!$user->isAdmin()) {
            Assert::eq($article->getAuthor()->getUuid(), $user->getUuid(), 'You can edit only YOURS articles');
        }

        $status = $this->articleService->delete($article);

        return new JsonResponse(['success' => $status]);
    }
}
