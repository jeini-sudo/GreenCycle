<?php

namespace App\Enums;

enum TreeStatus: string
{
    case Active = 'ACTIVE';
    case Mature = 'MATURE';
    case Dead = 'DEAD';
    case Harvested = 'HARVESTED';
}
