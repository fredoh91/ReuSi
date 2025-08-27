<?php
namespace App\Form\Model;

use App\Entity\Signal;
use App\Entity\ReleveDeDecision;
use App\Entity\ReunionSignal;

class SignalReleveReunionDTO
{
    public ?Signal $signal = null;
    public ?ReleveDeDecision $releve = null;
    public ?ReunionSignal $reunionSignal = null;
}