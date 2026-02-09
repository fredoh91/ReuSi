<?php

namespace App\Twig;

use App\Entity\Signal;
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
    
}