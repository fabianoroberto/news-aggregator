<?php

declare(strict_types=1);

namespace App\Controller\Rest;

use App\Controller\Rest\Traits\ErrorResponseTrait;
use App\Controller\Rest\Traits\HateoasResponseTrait;
use App\Dto\Request\CommentCreateRequest;
use App\Entity\Article;
use App\Entity\Comment;
use App\Repository\CommentRepositoryInterface;
use App\Service\CommentService;
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
 * @OA\Tag(name="Comments")
 */
class ApiCommentController extends ApiBaseController
{
    use ErrorResponseTrait;
    use HateoasResponseTrait;

    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private CommentService $commentService,
    ) {
    }

    /**
     * @Rest\Get("/v1/public/articles/{parentId}/comments", name="api_get_article_comments")
     * @ParamConverter("article", options={"mapping": {"parentId": "id"}})
     *
     * @OA\Parameter(
     *     in="query",
     *     name="page",
     *     description="Page from which to start listing article comments.",
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
     *                 "comment"
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
     *             ref=@Model(type=Comment::class, groups={"comment"})
     *         )
     *     )
     * )
     */
    public function index(Request $request, Article $article): Response
    {
        return $this->handleCollectionRequest(
            $this->commentRepository,
            $request,
            [
                'serializerGroups' => [
                    'comment',
                ],
            ],
            ['article' => $article->getId()]
        );
    }

    /**
     * @Rest\Get("/v1/public/articles/{parentId}/comments/{id}", name="api_get_article_comment")
     * @ParamConverter("article", options={"mapping": {"parentId": "id"}})
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
     *                 "comment"
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
     *             type=Comment::class,
     *             groups={
     *                 "comment"
     *             }
     *         )
     *     )
     * )
     */
    public function detail(Request $request, Article $article, Comment $comment): Response
    {
        Assert::eq($comment->getArticle()->getUuid(), $article->getUuid());

        $params = (new OptionsResolver())
            ->setDefaults([
                'serializerGroups' => [
                    'comment',
                ],
            ])
            ->resolve($request->query->all());

        return $this->serializeResponse($comment, $params['serializerGroups']);
    }

    /**
     * @Rest\Post("/v1/public/articles/{parentId}/comments", name="api_post_article_comments")
     * @ParamConverter("commentCreateRequest", converter="fos_rest.request_body")
     * @ParamConverter("article", options={"mapping": {"parentId": "id"}})
     *
     * @OA\RequestBody(
     *     @Model(type=CommentCreateRequest::class),
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
     *                 "comment"
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
     *             type=Comment::class,
     *             groups={"comment"}
     *         )
     *     )
     * )
     *
     * @throws \Exception
     */
    public function create(
        CommentCreateRequest $commentCreateRequest,
        Request $request,
        Article $article,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        if (\count($validationErrors) > 0) {
            $view = $this->constraintViolationView($validationErrors);

            return $this->handleView($view);
        }

        $params = (new OptionsResolver())
            ->setDefaults([
                'serializerGroups' => [
                    'comment',
                ],
            ])
            ->resolve($request->query->all());

        $comment = $this->commentService->store($commentCreateRequest, $article);

        return $this->serializeResponse($comment, $params['serializerGroups']);
    }

    /**
     * @Rest\Post("/v1/public/articles/{parentId}/comments/{id}/image", name="api_post_article_comments_image")
     * @ParamConverter("article", options={"mapping": {"parentId": "id"}})
     *
     * @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="multipart/form-data",
     *         @OA\Schema(
     *             @OA\Property(
     *                 description="File to upload",
     *                 property="photo",
     *                 type="string",
     *                 format="file",
     *             ),
     *             required={"file"}
     *         )
     *     )
     * ),
     * @OA\Parameter(
     *     description="ID of comment to update",
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
     *             type=Comment::class,
     *             groups={"comment"}
     *         )
     *     )
     * )
     */
    public function saveImage(Request $request, Article $article, Comment $comment): Response
    {
        Assert::eq($comment->getArticle()->getUuid(), $article->getUuid());

        $image = $request->files->get('photo');
        $params = (new OptionsResolver())
            ->setDefaults([
                'serializerGroups' => [
                    'article',
                ],
            ])
            ->resolve($request->query->all());

        $comment = $this->commentService->saveImage($comment, $image);

        return $this->serializeResponse($comment, $params['serializerGroups']);
    }

    /**
     * @Rest\Delete("/v1/articles/{parentId}/comments/{id}", name="api_delete_article_comments")
     * @ParamConverter("article", options={"mapping": {"parentId": "id"}})
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
     *                 "comment"
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
    public function delete(Article $article, Comment $comment): JsonResponse
    {
        $user = $this->getLoggedUser();

        if ($user->isAdmin()) {
            throw new \Exception('Only admin can delete a comment');
        }

        Assert::eq($comment->getArticle()->getUuid(), $article->getUuid());

        $status = $this->commentService->delete($comment);

        return new JsonResponse(['success' => $status]);
    }
}
