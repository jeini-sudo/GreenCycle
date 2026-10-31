<?php

namespace App\Enums;

enum TreeStatus: string
{
    case Active = 'active';
    case Mature = 'mature';
    case Dead = 'dead';
    case Harvested = 'harvested';
}
