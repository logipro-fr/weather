<?php

namespace Weather\Infrastructure\Api\v1\Symfony;

use Symfony\Component\HttpFoundation\InputBag;
use Weather\Application\ImportLegacy\ImportLegacy;
use Weather\Application\ImportLegacy\ImportLegacyRequest;
use Weather\Application\Presenter\PresenterJson;
use Weather\Application\Presenter\RequestInterface;
use Weather\Application\ServiceInterface;
use Weather\Domain\Model\Exceptions\InvalidArgumentException;
use Weather\Domain\Model\Weather\WeatherInfoRepositoryInterface;

class LegacyFileController extends RequestController{
    private const PATH_ARGUMENT = "path";

    public function __construct(private WeatherInfoRepositoryInterface $repository){
    }

    protected function createService(): ServiceInterface{
        return new ImportLegacy(new PresenterJson(), $this->repository);
    }

    protected function createRequest(InputBag $query): RequestInterface {
        if (null === $query->get(self::PATH_ARGUMENT)) {
            throw new InvalidArgumentException("no \"" . self::PATH_ARGUMENT .
            "\" given", self::INVALID_ARGUMENT_CODE);
        }
        /** @var string $path */
        $path = $query->get(self::PATH_ARGUMENT);
        if(!(is_dir($path) || is_file($path))){
            throw new InvalidArgumentException($path . " is not a valid file or directory", self::INVALID_ARGUMENT_CODE);
        }
        return new ImportLegacyRequest($path);
    }
}