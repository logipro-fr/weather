<?php

namespace Weather\Infrastructure\Api\v1\Symfony;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Weather\Application\Presenter\RequestInterface;
use Weather\Domain\Model\Exceptions\InvalidArgumentException;
use Weather\Infrastructure\Api\v1\Controller;

abstract class BaseController extends AbstractController
{
    public function execute(Request $request): Response
    {
        $controller = $this->createController();
        try {
            $controller->execute($this->createRequest($request->query));
        } catch (InvalidArgumentException $e) {
            $controller->writeUnsuccessfulResponse($e);
        }
        return new Response($controller->readResponse(), $controller->readStatus(), $controller->readHeaders());
    }

    abstract protected function createController(): Controller;
    abstract protected function createRequest(InputBag $query): RequestInterface;
}
