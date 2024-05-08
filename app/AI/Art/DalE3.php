<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 08/04/2024
 * Time: 1:10
 */

namespace App\AI\Art;

use App\AI\GenerateAIStatuses;
use App\Models\Image;
use OpenAI\Contracts\ClientContract;

class DalE3
{

    public function __construct(private ClientContract $client)
    {

    }

    public function create(Image $image)
    {
        $prompt = \Str::remove("Illustrate ", $image->prompt);

        $response = $this->client->images()->create([
            'model'           => 'dall-e-3',
            'prompt'          => $prompt,
            'n'               => 1,
            'size'            => '1024x1024',
            'response_format' => 'url',
        ]);

        \Log::debug("[DalE3][create] Got response for prompt {$prompt}", $response->toArray());

        $image->book()->increment("additional_data->costs_usd", "0.040");

        return new GenerateImageResult(\Str::uuid(), GenerateAIStatuses::Completed, [$response->data[0]->url]);
    }
}
