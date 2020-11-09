<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class CommonController extends AbstractController
{



//    protected function getAllRoutes()
//    {
//        /** @var Router $router */
//        $router = $this->get('router');
//        $routes = $router->getRouteCollection();
//        // dump($routes->all());
//        $rs = [];
//        foreach ($routes as $key => $route) {
//            if ($route->hasDefault('_controller')) {
//                if(str_starts_with($route->getDefault('_controller'),'App\Controller')){
//                    array_push($rs, [
//                        "name" =>$key,
//                        "path" =>$route->getPath(),
//                        "methode" => explode('::', $route->getDefault('_controller'))[1],
//                    ]);
//                }
//            }
//        }
//        return $rs;
//    }

}
