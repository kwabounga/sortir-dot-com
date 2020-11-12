<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Form\RegisterType;

use App\Services\CSVLoaderService;
use App\Services\FileUploader;
use App\Services\InitialisationService;
use App\Services\Msgr;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use function Sodium\add;

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
    public function afterLogin(Request $request, EntityManagerInterface $em) {
        // si authentifié bienvenue
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){
            $user = $this->getUser();
            $AppUser = $em->getRepository(User::class)->findOneBy(['username' => $user->getUsername()]);
            if($AppUser->getActif() == false){
                $this->get('security.token_storage')->setToken(null);
                $request->getSession()->invalidate();
                $this->addFlash(Msgr::TYPE_WARNING,Msgr::USERDESACTIVATED);
            } else {
                $this->addFlash(Msgr::TYPE_SUCCESS, Msgr::WELCOME.$this->getUser()->getUsername());
            }

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
     * @Route("/profile", name="profil")
     */
    public function profile(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, FileUploader $fu){
        if($this->isGranted('ROLE_ADMIN')){
            $this->addFlash(Msgr::TYPE_WARNING, 'en admin aller sur register pour avoir plus de modification');
        }
        $user = $em->getRepository(User::class)->findOneBy(['username' => $this->getUser()->getUsername()]);
        $current_role = $user->getRole();
        $current_campus = $user->getCampus();
        if($user === null){
            $this->addFlash(Msgr::TYPE_ERROR, Msgr::IMPOSSIBLE);
            return $this->redirectToRoute('main_home');
        }else {
            if($user->getPhoto() != null){
//                $photo= new File('uploads/photo/'.$user->getId().'/'.$user->getPhoto());
//                $user->setPhoto($photo);
            }
        }
        //$userForm = $this->createForm(RegisterType::class, $user);
        $registerForm = $this->createForm(RegisterType::class,$user);
        $registerForm->handleRequest($request);
        if($registerForm->isSubmitted() && $registerForm->isValid()){
            $photoFile = $registerForm->get('photo')->getData();

            if ($photoFile!== null) {

                $newFilename = $fu->upload($photoFile, $em->getRepository(\App\Entity\User::class)->findOneBy(['username' => $user->getUsername()]));
                $user->setPhoto($newFilename);
            } else {
                $pathParts = explode('/', $user->getPhoto());
                $photoName = $pathParts[count($pathParts)-1];
                //dump($logoName);
                $user->setPhoto($photoName);
            }
            // pour eviter de se  faire ecraser les roles avec le "form disabled"
            $user->setRole($current_role);
            $user->setCampus($current_campus);
//            $user->setDateCreated(new \DateTime());
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('profil');
        }else {
            if($user->getPhoto()){
//                $user->setPhoto(new File($user->getPhoto()));

            }
        }
        return $this->render("user/profile.html.twig", [
            'user' => $user,
            'register_form'=> $registerForm->createView(),
            'title' => 'Profil',
            // 'routes' => $this->getAllRoutes(),
        ]);
    }

    /**
     * @Route("/profile/show/{id}", name="profil_show",requirements={"id": "\d+"})
     */
    public function profileShow(Request $request, EntityManagerInterface $em, $id = null){
        $user = $em->getRepository(User::class)->find($id);
        if($user) {
            return $this->render("user/profile_show.html.twig", [
                'user' => $user,
                'title' => 'Profil',
                // 'routes' => $this->getAllRoutes(),
            ]);
        } else {
            $this->addFlash(Msgr::TYPE_INFOS,Msgr::USERNOEXIST);
            return $this->redirectToRoute('main_home');
        }
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
    /**
     * @Route("/admin/desactivate/{id}", name="desactivateUser")
     */
    public function desactivateUser(EntityManagerInterface $em , Request $request, $id =null){
        if($this->isGranted('ROLE_ADMIN')){
            $user = $em->getRepository(User::class)->find($id);
            $username = $user->getLastname().' '.$user->getFirstname();
            $user->setActif(false);
            $em->flush();
            $this->addFlash(Msgr::TYPE_INFOS, 'utilisateur '.$username.' désactivé');

        }
        return $this->redirectToRoute('home');
    }
    /**
     * @Route("/admin/activate/{id}", name="activateUser")
     */
    public function activateUser(EntityManagerInterface $em , Request $request, $id =null){
        if($this->isGranted('ROLE_ADMIN')){
            $user = $em->getRepository(User::class)->find($id);
            $username = $user->getLastname().' '.$user->getFirstname();
            $user->setActif(true);
            $em->flush();
            $this->addFlash(Msgr::TYPE_INFOS, 'utilisateur '.$username.' réactivé');
        }
        return $this->redirectToRoute('home');
    }
    /**
     * @Route("/new/password/", name="new_password")
     */
    public function newPassword(EntityManagerInterface $em , Request $request){

        $params = $request->request->all();
        if($params!= [] ){
            dump($params['_mail']);
            $user = $em->getRepository(User::class)->findOneBy(['mail'=>$params['_mail']]);
            if($user) {
                $this->addFlash(Msgr::TYPE_INFOS,Msgr::RAZMDPSENDED);
                $time = new \DateTime();
                $fTime = $time->format('d/M/yy');
                dump($fTime);
                $token = base64_encode($user->getMail().':'.$user->getDateCreated()->format('d/M/yy').':'.$fTime);
                // ici on simule un envoi de lien par mail
                // par exemple : http://localhost/sortir-dot-com/public/new/password/send?tokken=a3JvQG5lbmJvdXJnLmNvbToxMS9Ob3YvMjAyMDoxMi9Ob3YvMjAyMA%3D%3D
                // le lien est valable seulement le jour de la demande
                return $this->redirectToRoute('new_password_send',['tokken'=>$token]);
            } else {
                $this->addFlash(Msgr::TYPE_INFOS,Msgr::USERNOEXIST);
                return $this->render("user/raz.html.twig", [
                    'title'=>'Demande de mot de passe'
                ]);
            }
        }

        return $this->render("user/raz.html.twig", [
            'title'=>'Demande de mot de passe'
        ]);

    }
    /**
     * @Route("/new/password/send", name="new_password_send")
     */
    public function newPasswordSend(EntityManagerInterface $em , Request $request, UserPasswordEncoderInterface $encoder){
        $token = $request->query->all()['tokken'];

        dump($request->query->all());
        dump($token);
        $time = new \DateTime();
        $fTime = $time->format('d/M/yy');
        if($token!=null){
            $allUsers = $em->getRepository(User::class)->findAll();
            foreach ($allUsers as $user ){
                $t = base64_encode($user->getMail().':'.$user->getDateCreated()->format('d/M/yy').':'.$fTime);
                if($token == $t){
                    $newInfos = $request->request->all();
                    dump($newInfos);
                    if($newInfos != [] and $newInfos['_password'] and $newInfos['_passwordConfirm'] and trim($newInfos['_password']) != '' and trim($newInfos['_passwordConfirm']) != ''){
                        if(trim($newInfos['_password']) === trim($newInfos['_passwordConfirm'])){
                            $hash = $encoder->encodePassword($user, trim($newInfos['_password']));
                           $user-> setPassword($hash);
                           $em->persist($user);
                           $em->flush();
                           $this->addFlash(Msgr::TYPE_INFOS,Msgr::RAZMDPOK);
                            return $this->redirectToRoute('main_home');
                        }
                    }
                    return $this->render("user/raz_ok.html.twig", [
                        'title'=>'Demande de mot de passe',
                        'tokken'=>$token
                    ]);
                }
            }
        } else {
            return $this->redirectToRoute('main_home');

        }

//        return $this->redirectToRoute('main_home');
        return $this->render("user/raz_ok.html.twig", [
            'title'=>'Demande de mot de passe'
        ]);

    }
}
