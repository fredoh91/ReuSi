<?php

namespace App\Command;

use App\Repository\StatutSuiviRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:test_StatutSuiviRepository',
    description: 'Commande pour tester la méthode findLastStatutBySuivi du StatutSuiviRepository pendant le DEV',
)]
class TestStatutSuiviRepository extends Command
{
    private StatutSuiviRepository $statutSuiviRepository;

    /**
     * Undocumented function
     * ex : symfony console app:test_StatutSuiviRepository findLastStatutBySuivi 1
     *
     * @param StatutSuiviRepository $statutSuiviRepository
     */
    public function __construct(StatutSuiviRepository $statutSuiviRepository)
    {
        parent::__construct();
        $this->statutSuiviRepository = $statutSuiviRepository;
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'methode', 
                InputArgument::REQUIRED,
                'methode permettant de récupérer le dernier statut')
            ->addArgument(
                'suiviId', 
                InputArgument::REQUIRED,
                'suiviId pour lequel on veut récupérer le dernier statut')
            ->addOption(
                'debug',
                null, 
                InputOption::VALUE_NONE, 
                'Afficher des informations de debug pendant l\'exécution')
        ;
    }

    /**
     * cette méthode est lancée par la commande.
     * exemple de commande : symfony console app:test_StatutSuiviRepository findLastStatutBySuivi 1
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return integer
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $suiviId = $input->getArgument('suiviId');
        $methode = $input->getArgument('methode');

        if(!$methode){
            $io->error('Vous devez préciser la méthode à exécuter en argument.');
            return Command::FAILURE;
        }

        
        if (!$suiviId) {      
            $io->error('Cette commande doit recevoir en argument l\'identifiant du suivi pour lequel on veut récupérer le dernier statut.');
            return Command::FAILURE;
        }  

        if ($suiviId) {
            // $io->note(sprintf('You passed an argument: %s', $suiviId));
            if($methode=='findLastStatutBySuivi'){

                $statutSuivi = $this->statutSuiviRepository->findLastStatutBySuivi($suiviId);
                if ($statutSuivi) {
                    $io->success('Le dernier statut suivi est : ' . $statutSuivi->getLibStatut());
                } else {
                    $io->error('Aucun statut suivi trouvé pour le suivi d\'identifiant ' . $suiviId);
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
