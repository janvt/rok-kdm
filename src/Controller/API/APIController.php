<?php

namespace App\Controller\API;

use App\Exception\APIException;
use App\Service\Governor\GovernorDataService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class APIController extends AbstractController
{
    const HEADER_TOKEN = 'rok-kdm-api-token';
    const PARAM_TOKEN = 'api_token';

    /**
     * @Route("/governor", methods={"POST"}, name="create_governor")
     * @param Request $request
     * @param GovernorDataService $governorService
     * @return Response
     */
    public function governorCreateAction(
        Request $request,
        GovernorDataService $governorService
    ): Response
    {
        if (!$this->validateHeaderToken($request)) {
            return $this->accessDenied();
        }

        try {
            $govData = \json_decode($request->getContent());
            $gov = $governorService->createGovernor($govData);
        } catch (APIException $exception) {
            return new Response($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->json($gov);
    }

    /**
     * @Route("/governor/snapshot", methods={"POST"}, name="create_governor_snapshot")
     * @param Request $request
     * @param GovernorDataService $governorService
     * @return Response
     */
    public function governorAddSnapshotAction(
        Request $request,
        GovernorDataService $governorService
    ): Response
    {
        if (!$this->validateHeaderToken($request)) {
            return $this->accessDenied();
        }

        try {
            $snapshotData = \json_decode($request->getContent());
            $gov = $governorService->addSnapshot($snapshotData);
        } catch (APIException $exception) {
            return new Response($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->json($gov);
    }

    private function validateHeaderToken(Request $request): bool
    {
        return $request->headers->get(self::HEADER_TOKEN) === $this->getParameter(self::PARAM_TOKEN);
    }

    private function accessDenied(): Response
    {
        return new Response('Access denied', Response::HTTP_FORBIDDEN);
    }
}
