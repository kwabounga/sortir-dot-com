<?php


namespace App\Services;


use App\Entity\Ville;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Router;

class CommonTwigAccessor extends AbstractController

{
    public function getMAPBOXTOKEN(){
      return 'pk.eyJ1Ijoia3dhYm91bmdhIiwiYSI6ImNraGFkNTl4YjEzYm8ycW5xNWxtMmlxOWIifQ.aS9r210ZMptLtSbWLPfwpg';
    }
    public function getVilleFromCampus()
    {
        $ville = $this->getDoctrine()->getRepository(Ville::class)->findOneBy(['nom'=> $this->getUser()->getCampus()->getNom()]);
        return $ville;
    }
    public function getAllRoutes()
    {
        /** @var Router $router */
        $router = $this->get('router');
        $routes = $router->getRouteCollection();
        // dump($routes->all());
        $rs = [];
        foreach ($routes as $key => $route) {
            if ($route->hasDefault('_controller')) {
                if(str_starts_with($route->getDefault('_controller'),'App\Controller')){
                    array_push($rs, [
                        "name" =>$key,
                        "path" =>$route->getPath(),
                        "methode" => explode('::', $route->getDefault('_controller'))[1],
                    ]);
                }
            }
        }
        return $rs;
    }

}