<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Form\RegisterType;
use App\Services\CSVLoaderService;
use App\Services\InitialisationService;
use App\Services\Msgr;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        // TODO: enlever le force mode pour ne pas injecter de données moisies
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
     * @Route("/register/csv", name="register_csv", methods={"POST"})
     */
    public function registerByCSV(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        // TODO: verrouiller pour admin

        /** @var UploadedFile $csvFile */
        $csvFile = $request->files->get('upload');
        $csv = '';
        dump($csvFile);
        if($csvFile['file']){
            $csv = $csvFile['file']->openFile('r')->fread($csvFile['file']->getSize());
        }
        dump($csv);
        /** @var JsonResponse $output */
        $output = CSVLoaderService::loadUsersFromCSV($em,$encoder, $csv);;
        return $output;
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
        // TODO: verrouiller pour admin

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
                $user->setRole($em->getRepository(Role::class)->findOneBy(['value'=>'ROLE_USER']));
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
                // 'routes' => $this->getAllRoutes(),
                'title' => (($this->isGranted('ROLE_ADMIN')&&$user->getId()!=null)?'Modification Admin':'Nouvel Utilisateur'),
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
            // 'routes' => $this->getAllRoutes(),
        ]);
    }

    /**
     * @Route("/login/connexion", name="after_login")
     */
    public function afterLogin() {
        // si authentifié bienvenue
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){
            $this->addFlash(Msgr::TYPE_SUCCESS, Msgr::WELCOME.$this->getUser()->getUsername());
        }

        return $this->redirectToRoute('main_home');
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(){

    }

    // gestion des profils utilisateurs lambda (role user)
    /**
     * @Route("/profil", name="profil")
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
            return $this->redirectToRoute('profil');
        }
        return $this->render("user/profile.html.twig", [
            'user' => $user,
            'register_form'=> $userForm->createView(),
            'title' => 'Profil',
            // 'routes' => $this->getAllRoutes(),
        ]);
    }
    /*
     * suppression de compte attention faire en sorte de gérér une desactivation de base et de forcer si on veux la suppression
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
