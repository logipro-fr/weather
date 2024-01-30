<?php

namespace Weather\Infrastructure\Api\v1\Symfony;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function Safe\json_encode;

class HelloWorldController extends AbstractController
{
    private const ARRAY = ["World!",
        "Darkness my old friend",
        "There! General kenobi",
        "it's me, I was wondering if after all these years you'd like to meet ",
        "everybody, my name is Markiplier and welcome back to Ten Twillights as Tim's",
        "Work",
        "Kitty (Japanese: ハロー・キティ, Hepburn: Harō Kiti), also known by her real name Kitty White " .
            "(キティ・ホワイト, Kiti Howaito), is a fictional character created by Yuko Shimizu, " .
            "currently designed by Yuko Yamaguchi, and owned by the Japanese company Sanrio. (source: wikipedia)",
        "there is a bug in this code!!!\",\"there really\":\"isn't\""
    ];
    #[Route('/api/v1/hello', name: "hello world", methods: ['GET'])]
    public function helloWorld(Request $request): Response
    {
        $res = ["Hello" => self::ARRAY[array_rand(self::ARRAY)]];
        return new Response(json_encode($res), 200, ["Content-Type" => "application/json"]);
    }
}
