<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Router;

class ErrorController extends CommonController
{
    /*
     * Surcharge de la page d'erreur
     * TODO: voir comment récuperer le code d'erreur
     */
    public function show(string $garbage, Request $request): Response
    {
        dump($garbage);
        dump($this->getUser());

        return $this->render('bundles/TwigBundle/Exception/error404.html.twig', [
            'title' => 'Oups, Quelque Chose c\'est mal passé...',
            'uri' => $request->getUri(),
            // 'routes' => $this->getAllRoutes(),
        ]);
    }
}
