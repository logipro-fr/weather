<?php

namespace Weather\Infrastructure\Api\v1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloWorldController extends AbstractController
{
    #[Route('/api/v1/hello', name: "hello world", methods: ['GET'])]
    public function helloWorld(Request $request): Response
    {
        return new Response('{"Hello":"World!"}', 200, ["Content-Type" => "application/json"]);
    }
}
