<?php

namespace Weather\Infrastructure\Api\v1\Symfony;

use Safe\Exceptions\JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Weather\Application\Error\ErrorResponse;
use Weather\Application\Presenter\AbstractPresenter;
use Weather\Application\Presenter\ApiPresenter;
use Weather\Application\Presenter\RequestInterface;
use Weather\Application\ServiceInterface;
use Weather\Domain\Model\Exceptions\BaseException;
use Weather\Domain\Model\Exceptions\InvalidArgumentException;

use function Safe\json_decode;

abstract class RequestController extends AbstractController
{
    private const CODE_RANGE_LOW = 100;
    private const CODE_RANGE_HIGH = 599;
    private const CODE_UNKNOWN_INTERNAL = 500;

    private ServiceInterface $service;

    public function execute(Request $request): Response
    {
        $this->service = $this->createService();
        try {
            $this->service->execute($this->createRequest($request->query));
        } catch (BaseException $e) {
            $this->writeUnsuccessfulResponse($e);
        }
        return new Response($this->readResponse(), $this->readStatus(), $this->readHeaders());
    }

    abstract protected function createService(): ServiceInterface;
    abstract protected function createRequest(InputBag $query): RequestInterface;

    private function writeUnsuccessfulResponse(BaseException $e): void
    {
        $badResponse = new ErrorResponse(
            $e->getCode(),
            $e->getMessage(),
            $e->getType(),
            $e->getData()
        );
        $this->getPresenter()->write($badResponse);
    }

    private function getPresenter(): ApiPresenter
    {
        /** @var ApiPresenter $presenter */
        $presenter = $this->service->getPresenter();
        return $presenter;
    }

    private function readResponse(): string
    {
        return strval($this->getPresenter()->read());
    }

    private function readStatus(): int
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
    private function readHeaders(): array
    {
        return $this->getPresenter()->getHeaders();
    }
}
