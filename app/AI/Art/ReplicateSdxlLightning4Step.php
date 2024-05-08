<?php
/**
 * https://replicate.com/bytedance/sdxl-lightning-4step
 *
 * Created by PhpStorm.
 * User: omer
 * Date: 08/04/2024
 * Time: 1:10
 */

namespace App\AI\Art;

use App\AI\GenerateAIStatuses;
use App\Models\Image;
use BenBjurstrom\Replicate\Replicate;
use Log;
use Str;

class ReplicateSdxlLightning4Step
{

    public function __construct(private Replicate $client)
    {
    }

    public function create(Image $image): GenerateImageResult
    {
        $prompt = Str::remove("Illustrate ", $image->prompt);
        $version = '727e49a643e999d602a896c774a0658ffefea21465756a6ce24b7ea4165eba6a';

        $input   = [
            'prompt'          => $prompt,
            "negative_prompt" => "(lowres, low quality, worst quality:1.2), (text:1.2), watermark, painting, drawing, illustration, glitch, deformed, mutated, cross-eyed, ugly, disfigured (lowres, low quality, worst quality:1.2), (text:1.2), watermark, glitch,deformed, mutated, cross-eyed, ugly, disfigured, muslim, arab, Keffiyeh",
            'width'           => 1024,
            'height'          => 1024,
            'scheduler' => 'K_EULER',

//            'scheduler' => 'DPMSolverMultistep',
            'seed'            => $image->book->additional_data["imageSeed"] ?? null,
            'num_outputs' => 1,
            'num_inference_steps' => 4,
            'guidance_scale' => 0,
//            'guidance_scale' => 16.63,

            'disable_safety_checker' => true
        ];

        Log::debug("[ReplicateSdxlLightning4Step][create] Requesting ", $input + ["version" => $version]);

        $response = $this->client->predictions()
            ->withWebhook(config("app.url") . "/api/replicate/sdxl-lightning-4step/{$image->book->uuid}/{$image->id}")
            ->create($version, $input);

        Log::debug("[ReplicateSdxlLightning4Step][create] Got response for prompt {$prompt}", (array) $response);

        return new GenerateImageResult($response->id, GenerateAIStatuses::Initial, []);
    }
}
