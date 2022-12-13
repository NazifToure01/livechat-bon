<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\EditProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class EditProfilController extends AbstractController
{
    #[Route('/edit/profil/{id}', name: 'app_edit_profil')]
    public function index($id, EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $hasher): Response
    {
        $user = $entityManager->getRepository(Utilisateur::class)->find($id);
        $form = $this->createForm(EditProfilType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $directory = 'profilpics';
            $data = $form->getData();

            $file = $form['profilpic']->getData();
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $directory, $filename
            );
            $user->setPseudo($data->getPseudo());
            $user->setProfilpic($filename);

            $entityManager->flush();
            $this->addFlash('notice', 'Vos données ont été mise à jour avec succes');
            return $this->redirectToRoute('app_chat');
        }




        return $this->render('edit_profil/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
