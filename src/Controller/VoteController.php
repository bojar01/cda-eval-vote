<?php

namespace App\Controller;

use App\Entity\UserVote;
use App\Entity\Vote;
use App\Form\VoteType;
use App\Repository\CandidateRepository;
use App\Repository\UserVoteRepository;
use App\Repository\VoteRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/vote')]
class VoteController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN')] 
    #[Route('/', name: 'app_vote_index', methods: ['GET'])]
    public function index(VoteRepository $voteRepository): Response
    {
        return $this->render('vote/index.html.twig', [
            'votes' => $voteRepository->findAll(),
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/new', name: 'app_vote_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $vote = new Vote();
        $form = $this->createForm(VoteType::class, $vote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($vote);
            $entityManager->flush();

            return $this->redirectToRoute('app_vote_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vote/new.html.twig', [
            'vote' => $vote,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_vote_show', methods: ['GET'])]
    public function show(Vote $vote): Response
    {
        return $this->render('vote/show.html.twig', [
            'vote' => $vote,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/edit', name: 'app_vote_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Vote $vote, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VoteType::class, $vote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_vote_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vote/edit.html.twig', [
            'vote' => $vote,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_vote_delete', methods: ['POST'])]
    public function delete(Request $request, Vote $vote, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vote->getId(), $request->request->get('_token'))) {
            $entityManager->remove($vote);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_vote_index', [], Response::HTTP_SEE_OTHER);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/vote/{id}', name:'app_vote_vote', methods:['POST'])]
    public function vote(Request $request, VoteRepository $voteRepository , UserVoteRepository $userVoteRepository, CandidateRepository $candidateRepository ,EntityManagerInterface $entityManager, $id)
    {
        $voteId = $id;
        $vote = $voteRepository->find($voteId);

        if($vote){

            if ($this->isCsrfTokenValid('vote'.$vote->getId(), $request->request->get('_token'))) {
                $now = new DateTime();
                if($now > $vote->getElectionStart() and $now < $vote->getElectionEnd()){
                    
                    if($vote->getSessionId()->getId() == $this->getUser()->getSession()->getId()) {
                        
                        $userAlreadyVote = $userVoteRepository->findBy([
                            'vote' => $vote->getId(),
                            'user' => $this->getUser()->getId()
                        ]);

                        if(!$userAlreadyVote) {
                            $userVote = new UserVote();
                            $userVote->setUser($this->getUser());
                            $userVote->setVote($vote);

                            $candidate_selected = $request->request->get('selected');

                            if($candidate_selected){
                                if($candidate_selected == "null"){
                                } else {
                                    
                                    $candidate = $candidateRepository->findOneBy([
                                        'user' => $candidate_selected,
                                        'vote' => $vote
                                    ]);

                                    if($candidate) {
                                        $candidate->setVoteCount($candidate->getVoteCount() + 1);
                                        // dd('Add point to user');
                                    }
                                }
                            }
                            // dd($candidate_selected);

                            $entityManager->persist($userVote);
                            $entityManager->flush();

                            // dd('Vote pris en compte ');
                            $this->addFlash('success', 'Vote pris en compte');
                            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
                            
                        } else {
                            // Vous avez déjà voté pour ce vote 
                            $this->addFlash('warning', 'Vous avez déjà voté pour ce vote');
                            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
                            // dd('vous avez déja voté pour ce vote');
                        }
                    } else {
                        // On ne peut pas voter pour une autre session
                        $this->addFlash('warning', 'On ne vote pas pour une autre session');
                        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
                        // dd('Erreur session');
                    }
                } else {
                    // pas dans le créneau horraire
                    $this->addFlash('warning', 'Il n\'est plus possible d\'effectuer cette action');
                    return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
                    // dd('hors créneaux');
                }
            } else {
                // mauvais csrf
                // dd('vote n\'existe pas');
                $this->addFlash('warning', 'Petit coquin va! ;)');
                return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
            }
        } else {
            // le vote existe pas
            $this->addFlash('warning', 'Humm .. ce vote n\'existe pas.');
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
            // dd('vote n\'existe pas');
        }
    }
}
