<?php

namespace Weather\Infrastructure\Api\v1\Symfony;

use Weather\Domain\Model\Exceptions\InvalidArgumentException;
use Symfony\Component\HttpFoundation\InputBag;
use Weather\Application\FetchData\ById\FetchDataById;
use Weather\Application\FetchData\ById\FetchDataByIdRequest;
use Weather\Application\Presenter\PresenterJson;
use Weather\Domain\Model\Weather\WeatherInfoRepositoryInterface;

class GetExistingWeatherByIdController extends RequestController
{
    private const INVALID_ARGUMENT_CODE = 400;
    private const IDENTIFIER_ARGUMENT = "id";

    public function __construct(protected WeatherInfoRepositoryInterface $repository)
    {
    }

    protected function createService(): FetchDataById
    {
        $presenter = new PresenterJson();
        return new FetchDataById($presenter, $this->repository);
    }

    protected function createRequest(InputBag $query): FetchDataByIdRequest
    {
        if (null === $query->get(self::IDENTIFIER_ARGUMENT)) {
            throw new InvalidArgumentException("no identifier \"id\" given", self::INVALID_ARGUMENT_CODE);
        }
        /** @var string id */
        $id = $query->get(self::IDENTIFIER_ARGUMENT);
        return new FetchDataByIdRequest($id);
    }
}
