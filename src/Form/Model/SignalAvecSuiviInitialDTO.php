<?php

namespace App\Form\Model;

use App\Entity\Signal;
use App\Entity\ReleveDeDecision;
use App\Entity\Suivi;
use Symfony\Component\Validator\Constraints as Assert;

class SignalAvecSuiviInitialDTO
{
    #[Assert\Valid]
    public ?Signal $signal = null;

    #[Assert\Valid]
    public ?Suivi $suiviInitial = null;

    #[Assert\Valid]
    public ?ReleveDeDecision $rddInitial = null;
}
