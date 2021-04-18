<?php

declare(strict_types=1);

namespace App\Controller\Rest\Traits;

use App\Repository\Common\PaginatorInterface;
use FOS\RestBundle\Context\Context;
use Hateoas\Configuration\Route;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Hateoas\Representation\PaginatedRepresentation;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

trait HateoasResponseTrait
{
    protected function handleCollectionRequest(
        PaginatorInterface $repository,
        Request $request,
        array $defaultParams = [],
        array $overrideFilters = [],
    ): Response {
        $params = $this->getResolvedParams($request, $defaultParams);

        $mergedFilters = \array_merge($params['filters'], $overrideFilters);

        $pager = $repository->getPaginatorByFilters(
            $mergedFilters,
            $params['orderBy'],
            $params['page'],
            $params['limit']
        );

        return $this->serializePaginatedResponse($request, $pager, $params);
    }

    protected function getPaginatedRepresentation(
        Pagerfanta $pager,
        string $routeName,
        array $routeParams,
        array $params
    ): PaginatedRepresentation {
        $pagerFactory = new PagerfantaFactory('page', 'limit');

        return $pagerFactory->createRepresentation(
            $pager,
            new Route($routeName, \array_merge($params, $routeParams))
        );
    }

    /**
     * Serialize data with serializationGroups.
     *
     * @param mixed $data
     *
     * @return Response
     */
    protected function serializeResponse(
        $data,
        array $serializerGroups = [],
        int $statusCode = Response::HTTP_OK,
        array $headers = []
    ) {
        $context = (new Context())
            ->addGroups($serializerGroups);

        $view = $this->view($data, $statusCode, $headers)
            ->setContext($context);

        return $this->handleView($view);
    }

    protected function serializePaginatedResponse(Request $request, Pagerfanta $pager, array $params): Response
    {
        $data = $this->getPaginatedRepresentation(
            $pager,
            $request->get('_route'),
            $request->get('_route_params'),
            $params
        );

        $params['serializerGroups'][] = 'hateoas';

        return $this->serializeResponse($data, $params['serializerGroups']);
    }

    protected function getResolvedParams(Request $request, array $defaultParams = []): array
    {
        return (new OptionsResolver())
            ->setDefaults([
                'page' => $defaultParams['page'] ?? 1,
                'limit' => $defaultParams['limit'] ?? 50,
                'orderBy' => $defaultParams['orderBy'] ?? ['id' => 'ASC'],
                'filters' => $defaultParams['filters'] ?? [],
                'serializerGroups' => $defaultParams['serializerGroups'] ?? [],
                'parentId' => $defaultParams['parentId'] ?? [],
            ])
            ->setNormalizer('page', function (Options $options, $value) {
                return \filter_var($value, \FILTER_VALIDATE_INT);
            })
            ->setNormalizer('limit', function (Options $options, $value) {
                return \filter_var($value, \FILTER_VALIDATE_INT);
            })
            ->resolve($request->query->all());
    }
}
