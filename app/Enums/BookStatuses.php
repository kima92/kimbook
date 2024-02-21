<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 05/02/2024
 * Time: 19:09
 */

namespace App\Enums;

enum BookStatuses: int
{
    case Initial = 1;
    case GeneratingText = 2;
    case GeneratingImages = 3;
    case FailedText = 4;
    case FailedImages = 5;
    case Ready = 6;

    public function translated()
    {
        return trans("book_status_{$this->name}");
    }
}
