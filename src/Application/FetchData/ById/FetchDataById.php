<?php

namespace Weather\Application\FetchData\ById;

use Weather\Application\FetchData\FetchDataResponse;
use Weather\Application\Presenter\PresenterInterface;
use Weather\Domain\Model\Weather\WeatherInfoRepositoryInterface;

class FetchDataById
{
    public function __construct(
        private PresenterInterface $presenter,
        private WeatherInfoRepositoryInterface $repository
    ) {
    }

    public function execute(FetchDataByIdRequest $request): void
    {
        $res = $this->repository->findById($request->getRequestedId());
        $response = new FetchDataResponse($res);
        $this->presenter->write($response);
    }
}
