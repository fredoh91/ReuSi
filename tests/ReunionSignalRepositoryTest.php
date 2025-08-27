<?php

namespace App\Tests;

use App\Entity\ReunionSignal;
use App\Repository\ReunionSignalRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ReunionSignalRepositoryTest extends KernelTestCase
{
    private ReunionSignalRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->repository = static::getContainer()->get(ReunionSignalRepository::class);
    }

    public function testFindReunionsNotCancelledReturnsArray(): void
    {
        $results = $this->repository->findReunionsNotCancelled();
        $this->assertIsArray($results, 'Le résultat doit être un tableau');
        foreach ($results as $reunion) {
            $this->assertInstanceOf(ReunionSignal::class, $reunion);
        }
    }

    public function testFindReunionsNotCancelledRespectsDaysParameter(): void
    {
        $results100 = $this->repository->findReunionsNotCancelled(100);
        $results10 = $this->repository->findReunionsNotCancelled(10);

        // Affichage uniquement des dates de réunion dans la console
        fwrite(STDOUT, "Dates des réunions (100 jours) :\n");
        foreach ($results100 as $reunion) {
            fwrite(STDOUT, $reunion->getDateReunion()->format('d/m/Y') . "\n");
        }

        $this->assertIsArray($results100);
        $this->assertIsArray($results10);

        // En général, plus la période est courte, moins il y a de résultats
        $this->assertGreaterThanOrEqual(count($results10), count($results100));
    }

    public function testFindReunionsNotCancelledOnlyNotCancelled(): void
    {
        $results = $this->repository->findReunionsNotCancelled(3650); // 10 ans pour tout récupérer
        foreach ($results as $reunion) {
            $this->assertFalse($reunion->isReunionAnnulee(), 'La réunion ne doit pas être annulée');
        }
    }

    public function testFindReunionsNotCancelledOnlyRecent(): void
    {
        $days = 30;
        $dateLimit = new \DateTime(sprintf('-%d days', $days));
        $results = $this->repository->findReunionsNotCancelled($days);
        foreach ($results as $reunion) {
            $this->assertGreaterThan($dateLimit, $reunion->getDateReunion(), 'La réunion doit être récente');
        }
    }
}
