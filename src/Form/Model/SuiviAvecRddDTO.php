<?php

namespace App\Form\Model;

use App\Entity\Suivi;
use App\Entity\ReleveDeDecision;
use Symfony\Component\Validator\Constraints as Assert;

class SuiviAvecRddDTO
{
    #[Assert\Valid]
    public ?Suivi $suivi = null;

    #[Assert\Valid]
    public ?ReleveDeDecision $rddLie = null;
}