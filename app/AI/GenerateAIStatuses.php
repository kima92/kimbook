<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 16/04/2024
 * Time: 9:46
 */

namespace App\AI;

enum GenerateAIStatuses: int
{

    case Initial   = 1;
    case Running   = 2;
    case Completed = 3;
    case Failed    = 4;
    case Canceled  = 5;
}
