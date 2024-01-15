<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Vote;
use App\Form\CandidateType;
use App\Repository\CandidateRepository;
use App\Repository\UserRepository;
use App\Repository\VoteRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/candidate')]
class CandidateController extends AbstractController
{
    #[Route('/', name: 'app_candidate_index', methods: ['GET'])]
    public function index(CandidateRepository $candidateRepository): Response
    {
        return $this->render('candidate/index.html.twig', [
            'candidates' => $candidateRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_candidate_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $candidate = new Candidate();
        $form = $this->createForm(CandidateType::class, $candidate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($candidate);
            $entityManager->flush();

            return $this->redirectToRoute('app_candidate_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('candidate/new.html.twig', [
            'candidate' => $candidate,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_candidate_show', methods: ['GET'])]
    public function show(Candidate $candidate): Response
    {
        return $this->render('candidate/show.html.twig', [
            'candidate' => $candidate,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_candidate_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Candidate $candidate, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CandidateType::class, $candidate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_candidate_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('candidate/edit.html.twig', [
            'candidate' => $candidate,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_candidate_delete', methods: ['POST'])]
    public function delete(Request $request, Candidate $candidate, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $candidate->getId(), $request->request->get('_token'))) {
            $entityManager->remove($candidate);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_candidate_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/registeration/{id}', name: 'app_candidate_registeration', methods: ['POST'])]
    public function registeration(Request $request, VoteRepository $voteRepository, CandidateRepository $candidateRepository, EntityManagerInterface $entityManager, UserRepository $userRepository, $id,)
    {

        $voteId = $id;
        $vote = $voteRepository->find($voteId);

        if ($vote) {

            if ($this->isCsrfTokenValid('registeration' . $vote->getId(), $request->request->get('_token'))) {

                $now = new DateTime();

                if ($now > $vote->getApplicationStart() and $now < $vote->getApplicationEnd()) {
                    if ($vote->getSessionId()->getId() == $this->getUser()->getSession()->getId()) {

                        $userId = $this->getUser()->getId();

                        $candidate = $candidateRepository->findOneBy([
                            'vote' => $voteId,
                            'user' => $userId
                        ]);

                        if ($candidate) {

                            // On ne s'inscrit pas 2 fois à un vote
                            dd('déjà inscrit');
                        } else {
                            $user = $userRepository->find($userId);

                            $candidate = new Candidate();
                            $candidate->setUser($user);
                            $candidate->setVote($vote);
                            $candidate->setVoteCount(0);

                            $entityManager->persist($candidate);
                            $entityManager->flush();

                            dd("C'est ok !");
                        }
                    } else {
                        // ON NE VOTE PAS POUR UNE AUTRE SESSION
                        dd('probleme de session');

                    }
                } else {
                    // Nous ne sommes plus dans le créneau pour s'inscrire
                    dd('too late');
                }
            } else {
                dd('csrf invalide');
            }

        } else {
            // Vote n'existe pas
            dd("ce vote n'existe pas");
        }
    }

    #[Route('/unsuscribe/{id}', name: "app_candidate_unsuscribe", methods: ['POST'])]
    function unsuscribe(Request $request, CandidateRepository $candidateRepository, VoteRepository $voteRepository, EntityManagerInterface $entityManager, $id)
    {
        $voteId = $id;
        $vote = $voteRepository->find($voteId);

        if ($vote) {
            $userId = $this->getUser()->getId();

            $now = new DateTime();

            if ($now > $vote->getApplicationStart() and $now < $vote->getApplicationEnd()) {
                if ($vote->getSessionId()->getId() == $this->getUser()->getSession()->getId()) {
                    $candidate = $candidateRepository->findOneBy([
                        'vote' => $voteId,
                        'user' => $userId
                    ]);

                    if ($this->isCsrfTokenValid('unsuscribe' . $vote->getId(), $request->request->get('_token'))) {

                        if ($candidate) {
                            $entityManager->remove($candidate);
                            $entityManager->flush();
                            dd('ok');
                        } else {
                            // Candidat n'est pas inscrit pour ce vote
                            dd('Vous n\'etes pas inscrit à ce vote');
                        }
                    } else {
                        // Token Incorrect
                        dd('token incorrect');
                    }
                } else {
                    // On ne se désinscrit pas d'une autre session
                    dd('probleme de session');
                }
            } else {
                // Hors du créneau
                dd('Plus possible de se désinscrire.');
            }
        } else {
            // le vote n'existe pas !
            dd('Ce vote n\'existe pas');
        }
    }
}
