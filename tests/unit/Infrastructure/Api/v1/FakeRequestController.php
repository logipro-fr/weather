<?php

namespace Weather\Tests\Infrastructure\Api\v1;

use ReflectionClass;
use Symfony\Component\HttpFoundation\InputBag;
use Weather\Application\Presenter\RequestInterface;
use Weather\Application\ServiceInterface;
use Weather\Infrastructure\Api\v1\Symfony\RequestController;

// fetch_data_from_API: /api/v1/fetch
class FakeRequestController extends RequestController
{
    public function __construct(
        private ServiceInterface $service,
        private RequestInterface $request
    ) {
    }

    protected function createService(): ServiceInterface
    {
        return $this->service;
    }

    protected function createRequest(InputBag $query): RequestInterface
    {
        return $this->request;
    }
}
