<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{
    #[Route('/', name: 'app_chat')]

    public function index(MessageRepository $messageRepository, Request $request, EntityManagerInterface $entityManager): Response
    {

        $messges = $entityManager->getRepository(Message::class)->findAll();

        $newMessage = new Message();
        $form = $this->createForm(MessageType::class, $newMessage);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $date = new \DateTime();
            $user = $this->getUser();
            $data = $form->getData();
            $newMessage = new Message();
            $newMessage->setCorpsMessge($data->getCorpsmessge());
            $newMessage->setAuthor($user);
            $newMessage->setCreateAt($date);
            $entityManager->persist($newMessage);
            $entityManager->flush();
            return $this->redirectToRoute('app_chat');
        }

        return $this->render('chat/index.html.twig', [

            'form'=>$form->createView(),
            'messages'=>$messges

        ]);
    }

    #[Route('delete/message/{id}', name: 'app_delete_message')]

    public function DeleteMessage(EntityManagerInterface $entityManager, $id): Response
    {

        $messge = $entityManager->getRepository(Message::class)->findById($id);

        if ($messge && $messge[0]->getAuthor() == $this->getUser() ){
            $entityManager->remove($messge[0]);
            $entityManager->flush();

            $this->addFlash('notice','Message supprimé avec succès !');
            return $this->redirectToRoute('app_chat');
        }
        $this->addFlash('alert',"Vous n'avez pas le droit de supprimé ce message car vous n'êtes pas l'auteur !");
        return $this->redirectToRoute('app_chat');
    }


    #[Route('edit/message/{id}', name: 'app_edit_message')]

    public function EditMessage(EntityManagerInterface $entityManager, $id, Request $request): Response
    {
        $messges = $entityManager->getRepository(Message::class)->findAll();

        $messge = $entityManager->getRepository(Message::class)->find($id);

        if (!$messge || $messge->getAuthor() != $this->getUser() ){

            $this->addFlash('alert',"Vous ne pouvez pas editer ce message car vous n'êtes pas l'auteur");
            return $this->redirectToRoute('app_chat');
        }

        $form = $this->createForm(MessageType::class, $messge);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $messge->setCorpsMessge($data->getCorpsmessge());
           $entityManager->flush();

            $this->addFlash('notice',"Votre message à été modifier avec success");
            return $this->redirectToRoute('app_chat');
        }


        return $this->render('chat/index.html.twig', [

            'form'=>$form->createView(),
            'messages'=>$messges

        ]);
    }




}
