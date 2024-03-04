<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 05/02/2024
 * Time: 19:09
 */

namespace App\Enums;

enum PaymentStatuses: int
{
    case Initial = 1;
    case Succeed = 2;
    case Failed  = 3;
}
