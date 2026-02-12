<?php

namespace App\Controller;

use App\Entity\FichiersReunionsSignal;
use App\Entity\ReunionSignal;
use App\Entity\Signal;
use App\Entity\Suivi;
use App\Form\ReunionSignalDetailType;
use App\Service\FileUploaderService; // Changé de FileReunionSignalUploaderService
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReunionSignalDetailController extends AbstractController
{
    private readonly FileUploaderService $fileUploaderService; // Renommé la propriété
    private readonly Security $security;
    private readonly LoggerInterface $logger;

    public function __construct(
        FileUploaderService $fileUploaderService, // Changé le type-hint
        Security $security,
        LoggerInterface $logger
    ) {
        $this->fileUploaderService = $fileUploaderService; // Renommé la propriété
        $this->security = $security;
        $this->logger = $logger;
    }

    #[Route('/reunion_signal_detail/{reuSiId}/{tab}',
        name: 'app_reunion_signal_detail',
        requirements: ['reuSiId' => '\d+', 'tab' => 'nouveaux-signaux|suivis-signaux|nouveaux-fm|suivis-fm|details-reunion']
    )]
    public function reuSi_detail(
        EntityManagerInterface $em,
        Request $request,
        int $reuSiId,
        string $tab
    ): Response
    {
        $reunionSignal = $em->getRepository(ReunionSignal::class)->find($reuSiId);

        if (!$reunionSignal) {
            throw $this->createNotFoundException('La réunion avec l\'id ' . $reuSiId . ' n\'existe pas.');
        }

        $form = $this->createForm(ReunionSignalDetailType::class, $reunionSignal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->security->getUser();
            if (!$user) {
                throw $this->createAccessDeniedException('Utilisateur non connecté.');
            }
            $userName = $user->getUserName();

            // --- DÉBUT LOGIQUE UPLOAD ---
            $uploadedFiles = $form->get('fichiers')->getData();

            foreach ($uploadedFiles as $uploadedFile) {
                if ($uploadedFile) {
                    try {
                        $fichierReunionSignal = $this->fileUploaderService->upload(
                            'reunion_signal', // <-- type d'upload
                            $uploadedFile,
                            $reunionSignal,
                            $userName
                        );
                        $em->persist($fichierReunionSignal);
                    } catch (\Exception $e) {
                        $this->addFlash('error', sprintf('Erreur lors de l\'upload du fichier "%s" : %s', $uploadedFile->getClientOriginalName(), $e->getMessage()));
                        $this->logger->error(sprintf('Erreur upload fichier pour ReunionSignal ID %s : %s', $reunionSignal->getId(), $e->getMessage()));
                    }
                }
            }
            // --- FIN LOGIQUE UPLOAD ---

            $reunionSignal->setUpdatedAt(new \DateTimeImmutable())
                          ->setUserModif($userName);

            $em->flush();

            $this->addFlash('success', 'Les modifications ont été enregistrées.');

            return $this->redirectToRoute('app_reunion_signal_detail', [
                'reuSiId' => $reuSiId,
                'tab' => $tab,
            ]);
        }
        
        // Récupérer tous les suivis associés à cette réunion
        $suivis = $em->getRepository(Suivi::class)->findBy(
            ['reunionSignal' => $reunionSignal],
            ['id' => 'ASC'] // Tri par ID ou un autre critère pertinent
        );

        $fichiersLies = $reunionSignal->getFichiersReunionsSignals();

        // Préparation des listes pour les 4 catégories
        $nouveauxSignaux = [];
        $suivisSignaux = [];
        $nouveauxFaitsMarquants = [];
        $suivisFaitsMarquants = [];

        foreach ($suivis as $suivi) {
            $signal = $suivi->getSignalLie();
            if (!$signal) continue;

            if ($suivi->getNumeroSuivi() === 0) { // C'est un "Nouveau"
                if ($signal->getTypeSignal() === 'fait_marquant') {
                    $nouveauxFaitsMarquants[] = $suivi;
                } else {
                    $nouveauxSignaux[] = $suivi;
                }
            } else { // C'est un "Suivi de"
                if ($signal->getTypeSignal() === 'fait_marquant') {
                    $suivisFaitsMarquants[] = $suivi;
                } else {
                    $suivisSignaux[] = $suivi;
                }
            }
        }


        // On cherche les signaux et fait_marquant :
        //      - la date de suivi initial est strictement inférieure a la date de la réunion en cours
        //      ET aucun suivi n'est lié a la réunion en cours
        //      ET non-cloturé
        $signauxAnterieurs = $em->getRepository(Signal::class)->findSignauxAnterieursNonClotures('signal', $reunionSignal);
        $faitsMarquantsAnterieurs = $em->getRepository(Signal::class)->findSignauxAnterieursNonClotures('fait_marquant', $reunionSignal);

        return $this->render('reunion_signal_detail/reunion_signal_detail.html.twig', [
            'currentTab' => $tab,
            'reunionSignal' => $reunionSignal,
            'nouveauxSignaux' => $nouveauxSignaux,
            'suivisSignaux' => $suivisSignaux,
            'nouveauxFaitsMarquants' => $nouveauxFaitsMarquants,
            'suivisFaitsMarquants' => $suivisFaitsMarquants,
            'signauxAnterieurs' => $signauxAnterieurs,
            'faitsMarquantsAnterieurs' => $faitsMarquantsAnterieurs,
            'form' => $form->createView(),
            'fichiersLies' => $fichiersLies,
        ]);
    }

    #[Route('/reunion_signal/fichier/{id}/download', name: 'app_reunion_signal_fichier_download')]
    public function downloadFichierReunion(FichiersReunionsSignal $fichierReunion): BinaryFileResponse
    {
        $filePath = $this->fileUploaderService->getTargetDirectory('reunion_signal') . '/' . $fichierReunion->getNomFichier();

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('Le fichier n\'existe pas.');
        }

        return $this->file($filePath, $fichierReunion->getNomOriginal());
    }

    #[Route('/reunion_signal/fichier/{id}/delete', name: 'app_reunion_signal_fichier_delete', methods: ['POST'])]
    public function deleteFichierReunion(Request $request, EntityManagerInterface $em, FichiersReunionsSignal $fichierReunion): Response
    {
        // Vérifier le jeton CSRF
        if (!$this->isCsrfTokenValid('delete' . $fichierReunion->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
        } else {
            try {
                // Supprimer le fichier physique
                $this->fileUploaderService->delete('reunion_signal', $fichierReunion->getNomFichier());

                // Supprimer l'entité de la base de données
                $em->remove($fichierReunion);
                $em->flush();

                $this->addFlash('success', 'Fichier supprimé avec succès.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la suppression du fichier : ' . $e->getMessage());
                $this->logger->error(sprintf('Erreur suppression fichier ID %s : %s', $fichierReunion->getId(), $e->getMessage()));
            }
        }

        return $this->redirectToRoute('app_reunion_signal_detail', [
            'reuSiId' => $fichierReunion->getReunionSignalLiee()->getId(),
            'tab' => 'details-reunion' // rediriger vers l'onglet correct
        ]);
    }
}

