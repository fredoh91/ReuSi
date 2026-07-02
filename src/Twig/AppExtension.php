<?php

namespace App\Twig;

use App\Entity\Signal;
use App\Entity\ReunionSignal;
use App\Service\SignalStatusService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private $signalStatusService;

    public function __construct(SignalStatusService $signalStatusService)
    {
        $this->signalStatusService = $signalStatusService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_signal_status', [$this, 'getSignalStatus']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('humanize_status', [$this, 'humanizeStatus']),
            new TwigFilter('first_reunion_date', [$this, 'getFirstReunionDate']),
            new TwigFilter('last_reunion_date', [$this, 'getLastReunionDate']),
        ];
    }

    public function getSignalStatus(Signal $signal): string
    {
        return $this->signalStatusService->donneStatutSignalSuivi($signal);
    }

    public function humanizeStatus(string $status): string
    {
        return match ($status) {
            'presente' => 'Présenté',
            'prevu' => 'Prévu',
            'en_cours' => 'En cours',
            'cloture' => 'Clôturé',
            'en_cours_de_creation' => 'En cours de création',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }
    
    public function getFirstReunionDate(iterable $reunionSignals): ?\DateTime
    {
        if (empty($reunionSignals)) {
            return null;
        }

        $firstReunion = null;
        foreach ($reunionSignals as $reunionSignal) {
            if ($reunionSignal instanceof ReunionSignal) {
                $date = $reunionSignal->getDateReunion();
                if ($date !== null && ($firstReunion === null || $date < $firstReunion)) {
                    $firstReunion = $date;
                }
            }
        }

        return $firstReunion;
    }

    public function getLastReunionDate(iterable $reunionSignals): ?\DateTime
    {
        if (empty($reunionSignals)) {
            return null;
        }

        $lastReunion = null;
        foreach ($reunionSignals as $reunionSignal) {
            if ($reunionSignal instanceof ReunionSignal) {
                $date = $reunionSignal->getDateReunion();
                if ($date !== null && ($lastReunion === null || $date > $lastReunion)) {
                    $lastReunion = $date;
                }
            }
        }

        return $lastReunion;
    }
}
