<?php

namespace Weather\Application\FetchData\ByDateAndPoint;

use Weather\Application\FetchData\FetchDataResponse;
use Weather\Application\Presenter\PresenterInterface;
use Weather\Domain\Model\Weather\WeatherInfoRepositoryInterface;

class FetchDataByDateAndPoint
{
    public function __construct(
        private PresenterInterface $presenter,
        private WeatherInfoRepositoryInterface $repository
    ) {
    }

    public function execute(FetchDataByDateAndPointRequest $request): void
    {
        if ($request->isExact()) {
            $res = $this->repository->findByDateAndPoint(
                $request->getRequestedPoint(),
                $request->getRequestedDate(),
                $request->historical()
            );
        } else {
            $res = $this->repository->findCloseByDateAndPoint(
                $request->getRequestedPoint(),
                $request->getRequestedDate(),
                $request->historical()
            );
        }
        $response = new FetchDataResponse($res);
        $this->presenter->write($response);
    }
}
