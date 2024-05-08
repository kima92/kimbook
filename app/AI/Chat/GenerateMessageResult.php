<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 16/04/2024
 * Time: 9:46
 */

namespace App\AI\Chat;

use App\AI\GenerateAIStatuses;

class GenerateMessageResult
{

    public function __construct(public readonly string $id, public readonly GenerateAIStatuses $status, public readonly ?string $message) { }
}
