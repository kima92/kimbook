<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 16/04/2024
 * Time: 9:46
 */

namespace App\AI\Art;

use App\AI\GenerateAIStatuses;

class GenerateImageResult
{

    public function __construct(public readonly string $id, public readonly GenerateAIStatuses $status, public readonly array $images) { }
}
