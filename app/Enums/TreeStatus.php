<?php

namespace App\Enums;

enum TreeStatus: string
{
    case active = 'active';
    case mature = 'mature';
    case dead = 'dead';
    case harvested = 'harvested';
}
