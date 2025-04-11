<?php

namespace App\Service;

use App\Entity\Couple;
use App\Repository\CoupleRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiResponseService
{
    public function ok(array | null $data): JsonResponse
    {
        return $this->responseJson([
            'success' => 'true',
            'data' => $data,
        ], Response::HTTP_OK);
    }

    public function okRaw(int $data): Response
    {
        return new Response($data, Response::HTTP_OK);
    }

    public function error(string $message): JsonResponse
    {
        return $this->responseJson([
            'success' => 'false',
            'message' => $message,
        ], Response::HTTP_BAD_REQUEST);
    }
    private function responseJson(array $data, int $code): JsonResponse
    {
        $response = new JsonResponse();
        $response->setData($data);
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode($code);
        return $response;
    }
}
