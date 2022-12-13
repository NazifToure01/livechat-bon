<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\InscriptionType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class InscriptionController extends AbstractController
{
    #[Route('/inscription', name: 'app_inscription')]
    public function index(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $entityManager): Response

    {
        if($this->getUser()){
            return $this->redirectToRoute('app_chat');
        }
        $form =  $this->createForm(InscriptionType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $directory = 'profilpics';
            $data = $form->getData();

            $file = $form['profilpic']->getData();
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $directory, $filename
            );

            $newUser = new Utilisateur();
            $newUser->setEmail($data->getEmail());
            $newUser->setPseudo($data->getPseudo());
            $password =  $hasher->hashPassword($newUser, $data->getPassword());
            $newUser->setPassword($password);
            $newUser->setProfilpic($filename);
            $entityManager->persist($newUser);
            $entityManager->flush();
            return $this->redirectToRoute('app_chat');
        }

        return $this->render('inscription/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
