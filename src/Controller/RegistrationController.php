<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'register')]
    public function index(Request $request, EntityManagerInterface $em,UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createFormBuilder()
                        ->add(child:'username')
                        ->add('password', RepeatedType::class, [
                            'type' => PasswordTYpe::class,
                            'required' => true,
                            'first_options' => ['label' => 'Password'],
                            'second_options' => ['label' => 'Confirm Password'],
                        ])
                        ->add('submit',SubmitType::class, [
                            'attr' => [
                                'class' => 'btn btn-primary float-right'
                            ]
                        ] )
                        ->getForm();
                        $form->handleRequest($request);
                        $this->passwordHasher = $passwordHasher;
        if($form->isSubmitted()){
            $data = $form->getData();
            $user = new User();
            $user->setRoles(['ROLE_USER']);

            $user->setUsername($data['username']);
            $user->setPassword(
                $passwordHasher->hashPassword($user, $data['password'])
            );

            $em->persist($user);
            $em->flush();
            return $this->redirect($this->generateUrl(route:'app_login'));
        }
            
        return $this->render('registration/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
