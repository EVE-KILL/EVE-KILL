<?php

namespace EK\Controllers;

use EK\Http\Api\Controller;
use EK\Http\Attributes\RouteAttribute;

class Index extends Controller
{
    #[RouteAttribute('/test.json', ['GET'])]
    public function testjson()
    {
        //throw new \Exception('test');
        return $this->json(['message' => 'Hello World!']);
    }

    #[RouteAttribute('/[{name}]', ['GET'])]
    public function index(?string $name = 'Derp')
    {
        return $this->render('index.twig', ['name' => $name]);
    }
}
