<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/register/{id}", name="register")
     */
    public function register(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, $id = null)
    {
        if($this->isGranted('ROLE_ADMIN')){
            if ($id){
                $user = $this->getDoctrine()->getRepository(User::class)->find($id);
                if(!$user){
                    $this->addFlash('error', 'pas possible de faire ca ');
                    $user = new User();
                }
            } else {
                $user = new User();
            }

            $registerForm = $this->createForm(RegisterType::class,$user);
            $registerForm->handleRequest($request);
            if($registerForm->isSubmitted() && $registerForm->isValid()){
                $user->setDateCreated(new \DateTime());
                $d = new \DateTime();
                $hash = $encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($hash);
                $em->persist($user);
                $em->flush();
                return $this->redirectToRoute('home');
            }
            return $this->render('user/register.html.twig', [
                'page_name' => 'Register',
                'register_form' => $registerForm->createView(),
                'user' => $user

            ]);
        } else {
            $this->addFlash('error', 'vous devez etre admin pour venir ici');
            return $this->redirectToRoute('login');
        }

    }

    /**
     * @Route("/login", name="login")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function login(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder){
        return $this->render("user/login.html.twig", [
            'page_name' => 'Login',

        ]);
    }
    /**
     * @Route("/logout", name="logout")
     */
    public function logout(){

    }
    /**
     * @Route("/profile", name="profile")
     */
    public function profile(){
        $userForm = $this->createForm(RegisterType::class, $this->getUser());

        return $this->render("user/profile.html.twig", [
            'page_name' => 'profile',
            'user' => $this->getUser(),
            'register_form'=> $userForm->createView()
        ]);
    }
//    /**
//     * @Route("/delete", name="delete")
//     */
//    public function delete(EntityManagerInterface $em , Request $request){
//        if($this->isGranted('ROLE_ADMIN')){
//            $user = $this->getUser();
//            //$eUser = $em->getRepository(User::class)->findOneBy(['username'=> $user->getUsername()]);
//            $em->remove($user);
//            $em->flush();
//            $this->get('security.token_storage')->setToken(null);
//            $request->getSession()->invalidate();
//
//        }
//        return $this->redirectToRoute('home');
//    }
}
