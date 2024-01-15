<?php

namespace App\Controller;

use App\Form\AccountType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/account')]
class AccountController extends AbstractController
{
    
    #[Route('/', name: 'app_account')]
    public function index(UserRepository $userRepository): Response
    {
        $user = $userRepository->find($this->getUser()->getId());

        return $this->render('account/show.html.twig', [
            'controller_name' => 'AccountController',
            'user' => $user
        ]);
    }

    #[Route('/edit', name: 'app_account_edit')]
    public function edit(Request $request, UserRepository $userRepository, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->find($this->getUser()->getId());

        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if($imageFile){
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('picture_profil_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {

                }
                $user->setImage($newFilename);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
