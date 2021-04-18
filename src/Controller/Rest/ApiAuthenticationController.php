<?php

declare(strict_types=1);

namespace App\Controller\Rest;

use App\Dto\Request\UserCreatePasswordResetRequest;
use App\Dto\Request\UserSetPasswordRequest;
use App\Exception\AppException;
use App\Service\UserService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use FOS\RestBundle\Controller\Annotations as Rest;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * @OA\Tag(name="Authentication")
 */
class ApiAuthenticationController extends ApiBaseController
{
    public function __construct(
        private RefreshTokenManagerInterface $refreshTokenManager,
        private UserService $userService
    ) {
    }

    /**
     * @Rest\Post("/v1/auth/logout", name="api_post_logout")
     * @IsGranted("ROLE_USER")
     *
     * @OA\RequestBody(
     *     @OA\Schema(
     *         required={"refresh_token"},
     *         @OA\Property(
     *             type="string", property="refresh_token", description="Refresh token"
     *         )
     *     )
     * )
     *
     * @OA\Response(
     *     response=Response::HTTP_OK,
     *     description="Success",
     * )
     *
     * @Rest\View(statusCode=200)
     */
    public function logout(Request $request)
    {
        $refreshTokenParam = $request->request->get('refresh_token');

        $refreshToken = $this->refreshTokenManager->get($refreshTokenParam);
        $jwtUser = $this->getJwtUser();

        /* @phpstan-ignore-next-line */
        if ($refreshToken === null) {
            throw new AppException('This refresh token does not exist!', ['refreshToken' => $refreshTokenParam]);
        }

        if ($refreshToken->getUsername() !== $jwtUser->getUsername()) {
            throw new AppException('Refresh token is not valid!');
        }

        $this->refreshTokenManager->delete($refreshToken);

        return true;
    }

    /**
     * @Rest\Post("/v1/auth/password-reset-request", name="api_post_password_reset_request")
     * @ParamConverter("createPasswordResetRequest", converter="fos_rest.request_body")
     *
     * @OA\RequestBody(
     *     @Model(type=UserCreatePasswordResetRequest::class)
     * )
     *
     * @OA\Response(
     *     response=Response::HTTP_OK,
     *     description="Success",
     * )
     *
     * @Rest\View(statusCode=200)
     *
     * @throws AppException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransportExceptionInterface
     */
    public function passwordResetRequest(UserCreatePasswordResetRequest $createPasswordResetRequest): bool
    {
        return $this->userService->handlePasswordResetRequest($createPasswordResetRequest->getEmail());
    }

    /**
     * @Rest\Post("/v1/auth/set-password", name="api_post_set_password")
     * @ParamConverter("setPasswordRequest", converter="fos_rest.request_body")
     *
     * @OA\RequestBody(
     *     @Model(type=UserSetPasswordRequest::class)
     * )
     *
     * @OA\Response(
     *     response=Response::HTTP_OK,
     *     description="Success",
     * )
     *
     * @Rest\View(statusCode=200)
     *
     * @throws AppException
     */
    public function setPassword(UserSetPasswordRequest $setPasswordRequest): JsonResponse
    {
        $status = $this->userService->handleSetPasswordRequest($setPasswordRequest);

        return new JsonResponse(['success' => $status]);
    }
}
