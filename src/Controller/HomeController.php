<?php

namespace App\Controller;

use App\Entity\Vote;
use App\Repository\VoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class HomeController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/', name: 'app_home')]
    public function index(VoteRepository $voteRepository): Response
    {
        $votes = [];

        if($this->getUser()->getSession()){
            $session_id = $this->getUser()->getSession()->getId();
            $votes = $voteRepository->findBy(['session_id' => $session_id]);
        }

        // dd($votes);
        

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'votes' => $votes
        ]);
    }
}
