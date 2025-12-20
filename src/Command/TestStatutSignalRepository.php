<?php

namespace App\Command;

use App\Repository\StatutSignalRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:test_StatutSignalRepository',
    description: 'Commande pour tester la méthode findLastStatutBySignal du StatutSignalRepository pendant le DEV',
)]
class TestStatutSignalRepository extends Command
{
    private StatutSignalRepository $statutSignalRepository;

    /**
     * Undocumented function
     * ex : symfony console app:test_StatutSignalRepository findLastStatutBySignal 1
     *
     * @param StatutSignalRepository $statutSignalRepository
     */
    public function __construct(StatutSignalRepository $statutSignalRepository)
    {
        parent::__construct();
        $this->statutSignalRepository = $statutSignalRepository;
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'methode', 
                InputArgument::REQUIRED,
                'methode permettant de récupérer le dernier statut')
            ->addArgument(
                'signalId', 
                InputArgument::REQUIRED,
                'signalId pour lequel on veut récupérer le dernier statut')
            ->addOption(
                'debug',
                null, 
                InputOption::VALUE_NONE, 
                'Afficher des informations de debug pendant l\'exécution')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $signalId = $input->getArgument('signalId');
        $methode = $input->getArgument('methode');

        if(!$methode){
            $io->error('Vous devez préciser la méthode à exécuter en argument.');
            return Command::FAILURE;
        }

        
        if (!$signalId) {      
            $io->error('Cette commande doit recevoir en argument l\'identifiant du signal pour lequel on veut récupérer le dernier statut.');
            return Command::FAILURE;
        }  

        if ($signalId) {
            // $io->note(sprintf('You passed an argument: %s', $signalId));
            if($methode=='findLastStatutBySignal'){

                $statutSignal = $this->statutSignalRepository->findLastStatutBySignal($signalId);
                if ($statutSignal) {
                    $io->success('Le dernier statut signal est : ' . $statutSignal->getLibStatut());
                } else {
                    $io->error('Aucun statut signal trouvé pour le signal d\'identifiant ' . $signalId);
                }
                return Command::SUCCESS;

            } else {
                $io->error('La méthode spécifiée n\'est pas reconnue.');
                return Command::FAILURE;
            }
        } 

        // if ($input->getOption('debug')) {
        //     // ...
        // }

        // $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

    }
}
