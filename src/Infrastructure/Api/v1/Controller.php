<?php

namespace Weather\Infrastructure\Api\v1;

use Weather\Application\Presenter\AbstractPresenter;
use Exception;
use Safe\Exceptions\JsonException;
use Weather\Application\Error\ErrorResponse;
use Weather\Application\Presenter\RequestInterface;
use Weather\Application\ServiceInterface;
use Weather\Domain\Model\Exceptions\BaseException;

use function Safe\json_decode;
use function SafePHP\strval;

class Controller
{
    private const CODE_RANGE_LOW = 100;
    private const CODE_RANGE_HIGH = 699;
    private const CODE_UNKNOWN_INTERNAL = 500;

    public function __construct(
        private ServiceInterface $service
    ) {
    }

    public function execute(RequestInterface $request): void
    {
        try {
            $this->service->execute($request);
        } catch (BaseException $e) {
            $this->writeUnsuccessfulResponse($e);
        }
    }

    public function writeUnsuccessfulResponse(BaseException $e): void
    {
        try {
            /** @var \stdClass $message */
            $message = json_decode($e->getMessage());
        } catch (JsonException) {
            $message = $e->getMessage();
        }
        $badResponse = new ErrorResponse(
            $e->getCode(),
            $message,
            $e->getType()
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
        //throw new Exception($this->getPresenter()->getCode(), $this->getPresenter()->getCode());
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
