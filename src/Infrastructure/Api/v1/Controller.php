<?php

namespace Weather\Infrastructure\Api\v1;

use Weather\Application\Presenter\AbstractPresenter;
use Exception;
use Weather\Application\Error\ErrorResponse;
use Weather\Application\Presenter\RequestInterface;
use Weather\Application\ServiceInterface;

use function SafePHP\strval;

class Controller
{
    private const CODE_RANGE_LOW = 100;
    private const CODE_RANGE_HIGH = 599;
    private const CODE_UNKNOWN_INTERNAL = 500;

    public function __construct(
        private ServiceInterface $service
    ) {
    }

    public function execute(RequestInterface $request): void
    {
        try {
            $this->service->execute($request);
        } catch (Exception $e) {
            $this->writeUnsuccessfulResponse($e);
        }
    }

    private function writeUnsuccessfulResponse(Exception $e): void
    {
        $badResponse = new ErrorResponse(
            $e->getCode(),
            $e->getMessage()
        );
        $this->getPresenter()->write($badResponse);
    }

    private function getPresenter(): AbstractPresenter
    {
        return $this->service->getPresenter();
    }

    public function readResponse(): string
    {
        return strval($this->getPresenter()->read());
    }

    public function readStatus(): int
    {
        $code = $this->getPresenter()->getCode();
        if ($code < self::CODE_RANGE_LOW || $code > self::CODE_RANGE_HIGH) {
            return self::CODE_UNKNOWN_INTERNAL;
        }
        return $code;
    }

    /**
     * @return array<string,int|string>
     */
    public function readHeaders(): array
    {
        return $this->getPresenter()->getHeaders();
    }
}
