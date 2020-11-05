<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Role;
use App\Entity\User;
use App\Form\RegisterType;
use App\Services\CSVLoaderService;
use App\Services\InitialisationService;
use App\Services\Msgr;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends CommonController
{
    /*
     * Route spéciale pour initialiser / mettre à jour la base de donnée
     * (parametre d'environement app.admin_login et app.admin_password pour initialiser le mot de passe admin)
     */
    /**
     * @Route("/update/bdd/{force}", name="update_bdd")
     */
    public function updateBdd(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, $force = null)
    {
        $force= 'force';
        $roles = $em->getRepository(Role::class)->findAll();
        if(count($roles) === 0 ){
            InitialisationService::firstInitBdd($em, $encoder,$this->getParameter('app.admin_login'),$this->getParameter('app.admin_password'),(($force == 'force')?__DIR__.'/script.sql':null));
            $this->addFlash('infos', 'Premiere initialisation');
        }
        return $this->redirectToRoute('main_home');
    }

    /*
     * Creation d'utilisateurs en masse
     * avec un csv
     */
    /**
     * @Route("/register/csv", name="register_csv")
     */
    public function registerByCSV(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, $id = null)
    {
        // TODO: verrouiller pour admin
        $csv = "1; 2; 'theUsername1'; 'mail1@mail.com'; 'theUsername1'; 'firstname1'; 'lastname1'; 'phone1';
1; 2; 'theUsername2'; 'mail2@mail.com'; 'theUsername2'; 'firstname2'; 'lastname2'; 'phone2';
1; 2; 'theUsername3'; 'mail3@mail.com'; 'theUsername3'; 'firstname3'; 'lastname3'; 'phone3';
1; 2; 'theUsername4'; 'mail4@mail.com'; 'theUsername4'; 'firstname4'; 'lastname4'; 'phone4';
1; 2; 'theUsername5'; 'mail5@mail.com'; 'theUsername5'; 'firstname5'; 'lastname5'; 'phone5';
1; 2; 'theUsername6'; 'mail6@mail.com'; 'theUsername6'; 'firstname6'; 'lastname6'; 'phone6';
1; 2; 'theUsername7'; 'mail7@mail.com'; 'theUsername7'; 'firstname7'; 'lastname7'; 'phone7';";
        $op = CSVLoaderService::loadUsersFromCSV($em,$encoder, $csv);
         dump($op);
        // TODO: renviyer sur user List
        return $this->render('main/home.html.twig', [
            'routes' => $this->getAllRoutes(),
            'title' => 'Home',
        ]);
    }
    /*
     * Creation d'utilisateurs
     * seul un role admin peut le faire
     * (ou) si il ni a aucun utilisateur:  la creation du compte admin est possible
     */
    /**
     * @Route("/register/{id}", name="register")
     */
    public function register(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, $id = null)
    {
        /* aide memoire
        // reconnection si ca vient d'un remember me
        // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // connection possible d'un remember me
        // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        */

        // verification de la presence de user en bdd
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        // check si admin ou si il ni a aucun user
        if($this->isGranted('ROLE_ADMIN') || count($users) === 0){
            // si admin et id fourni : recuperation du user by id
            if ($id){
                $user = $this->getDoctrine()->getRepository(User::class)->find($id);
                if(!$user){
                    // si aucun user trouvé avec l'id
                    $this->addFlash(Msgr::TYPE_ERROR, Msgr::IMPOSSIBLE);
                    $this->addFlash(Msgr::TYPE_INFOS, 'nouvel utilisateur');
                    $user = new User();
                }
            } else{
                // si co et pas d'id : nouvel user
                $user = new User();
            }

            // récupération du formulaire et traitement
            $registerForm = $this->createForm(RegisterType::class,$user);
            $registerForm->handleRequest($request);

            if($registerForm->isSubmitted() && $registerForm->isValid()){

                $user->setDateCreated(new \DateTime());
                $hash = $encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($hash);
                $em->persist($user);
                $em->flush();

                if(count($users) === 0){
                    // ici premiere initialisation de la bdd lors de l'enregistrement
                    InitialisationService::firstInitBdd($em, $encoder,$this->getParameter('app.admin_login'),$this->getParameter('app.admin_password'),__DIR__.'/script.sql');
                }
                return $this->redirectToRoute('home');

            } else if(count($users) === 0){

                $this->addFlash(Msgr::TYPE_INFOS, Msgr::FIRST_CONNEXION);

            }
            return $this->render('user/register.html.twig', [
                'page_name' => 'Register',
                'register_form' => $registerForm->createView(),
                'user' => $user,
                'routes' => $this->getAllRoutes(),
                'title' => 'Nouvel Utilisateur',
            ]);

        } else {
            $this->addFlash(Msgr::TYPE_ERROR, Msgr::MUST_BE_ADMIN);
            return $this->redirectToRoute('login');
        }

    }


    /*
     *  cf security.yaml security: firewalls: main: form_login / logout params & provider
     */
    /**
     * @Route("/login", name="login")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function login(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder){
        $user = $this->getUser();
        return $this->render("user/login.html.twig", [
            "user" => $user,
            'title' => 'Login',
            'routes' => $this->getAllRoutes(),
        ]);
    }
    /**
     * @Route("/logout", name="logout")
     */
    public function logout(){

    }

    // gestion des profils utilisateurs lambda (role user)
    /**
     * @Route("/profile", name="profile")
     */
    public function profile(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder){
        if($this->isGranted('ROLE_ADMIN')){
            $this->addFlash(Msgr::TYPE_WARNING, 'en admin aller sur register pour avoir plus de modification');
        }
        $user = $this->getUser();
        $userForm = $this->createForm(RegisterType::class, $user);
        $registerForm = $this->createForm(RegisterType::class,$user);
        $registerForm->handleRequest($request);
        if($registerForm->isSubmitted() && $registerForm->isValid()){
            $user->setDateCreated(new \DateTime());
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('profile');
        }
        return $this->render("user/profile.html.twig", [
            'user' => $user,
            'register_form'=> $userForm->createView(),
            'title' => 'Profile',
            'routes' => $this->getAllRoutes(),
        ]);
    }
    /*
     * suppression de compte   attention faire en sorte de gérér une desactivation de base et de forcer si on veux la suppression
     */

    /**
     * @Route("/admin/delete/{id}", name="delete")
     * @Route("/admin/delete/{id}/{force}", name="delete_force")
     */
    public function delete(EntityManagerInterface $em , Request $request, $id =null, $force = null){
        if($this->isGranted('ROLE_ADMIN')){
            $user = $em->getRepository(User::class)->find($id);
            $username = $user->getLastname().' '.$user->getFirstname();
            if($force == 'force'){
                $em->remove($user);
                $em->flush();

                $this->addFlash(Msgr::TYPE_WARNING, 'utilisateur '.$username.' supprimé');
            } else {
                $user->setActif(false);
                $em->flush();

                $this->addFlash(Msgr::TYPE_INFOS, 'utilisateur '.$username.' désactivé');
            }

        }

        return $this->redirectToRoute('home');
    }
}
