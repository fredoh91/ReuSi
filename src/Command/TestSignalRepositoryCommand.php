<?php

namespace App\Command;

use App\Repository\ReunionSignalRepository;
use App\Repository\SignalRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test_SignalRepository',
    description: 'Teste les méthodes de recherche du SignalRepository',
)]
class TestSignalRepositoryCommand extends Command
{
    public function __construct(
        private SignalRepository $signalRepository,
        private ReunionSignalRepository $reunionSignalRepository        
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('reunionId', InputArgument::REQUIRED, 'ID de la réunion (ex: 12)')
            ->addArgument('type', InputArgument::OPTIONAL, 'Type : signal ou fait_marquant', 'signal')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $reunionId = $input->getArgument('reunionId');
        $type = $input->getArgument('type');
        // 1. Récupérer la réunion
        $reunion = $this->reunionSignalRepository->find($reunionId);
        if (!$reunion) {
            $io->error("La réunion avec l'ID $reunionId n'existe pas.");
            return Command::FAILURE;
        }
        $io->title("Test de findSignauxAnterieursNonClotures()");
        $io->info("Cible : Type [$type] | Date de réunion : " . $reunion->getDateReunion()->format('d/m/Y'));
        // 2. Appeler la fonction du Repository
        $results = $this->signalRepository->findSignauxAnterieursNonClotures($type, $reunion);
        // 3. Afficher les résultats sous forme de tableau
        if (empty($results)) {
            $io->warning("Aucun signal antérieur non clôturé trouvé pour cette réunion.");
        } else {
            $io->success(count($results) . " résultats trouvés.");
            $rows = [];
            foreach ($results as $signal) {
                $rows[] = [
                    $signal->getId(),
                    $signal->getTitre(),
                    $signal->getCreatedAt()->format('d/m/Y H:i'),
                    $signal->getTypeSignal()
                ];
            }
            $io->table(['ID', 'Titre', 'Date Création', 'Type'], $rows);
        }
        return Command::SUCCESS;
    }
}
