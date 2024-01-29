<?php

namespace Weather\Application\FetchData\ById;

use Weather\Application\FetchData\FetchDataResponse;
use Weather\Application\Presenter\AbstractPresenter;
use Weather\Application\Presenter\RequestInterface;
use Weather\Application\ServiceInterface;
use Weather\Domain\Model\Weather\WeatherInfoRepositoryInterface;

class FetchDataById implements ServiceInterface
{
    public function __construct(
        private AbstractPresenter $presenter,
        private WeatherInfoRepositoryInterface $repository
    ) {
    }

    /**
     * @param FetchDataByIdRequest $request
     */
    public function execute(RequestInterface $request): void
    {
        $res = $this->repository->findById($request->getRequestedId());
        $response = new FetchDataResponse($res);
        $this->presenter->write($response);
    }

    public function getPresenter(): AbstractPresenter
    {
        return $this->presenter;
    }
}
