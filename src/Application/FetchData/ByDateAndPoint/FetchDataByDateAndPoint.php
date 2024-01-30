<?php

namespace Weather\Application\FetchData\ByDateAndPoint;

use Weather\Application\FetchData\FetchDataResponse;
use Weather\Application\Presenter\AbstractPresenter;
use Weather\Application\Presenter\RequestInterface;
use Weather\Application\ServiceInterface;
use Weather\Domain\Model\Weather\WeatherInfoRepositoryInterface;

class FetchDataByDateAndPoint implements ServiceInterface
{
    public function __construct(
        private AbstractPresenter $presenter,
        private WeatherInfoRepositoryInterface $repository
    ) {
    }

    /**
     * @param FetchDataByDateAndPointRequest $request
     */
    public function execute(RequestInterface $request): void
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

    public function getPresenter(): AbstractPresenter
    {
        return $this->presenter;
    }
}
