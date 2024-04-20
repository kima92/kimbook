<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 08/04/2024
 * Time: 1:10
 */

namespace App\AI\Art;

use App\Models\Character;
use App\Models\Image;
use BenBjurstrom\Replicate\Replicate;
use Log;
use Str;

class ReplicateInstantId
{

    public function __construct(private Replicate $client)
    {
    }

    public function create(Image $image): GenerateImageResult
    {
        $prompt = Str::remove("Illustrate ", $image->prompt);
        $version = '6af8583c541261472e92155d87bba80d5ad98461665802f2ba196ac099aaedc9';

        $imgPath = null;
        if ($characterId = $image->book->additional_data["request"]["character"] ?? null) {
            $imgPath = url(Character::find($characterId)->image_path);
        }

        $input   = [
//            'model'           => 'stable-diffusion-2-1',
            'image'           => $imgPath,
            'prompt'          => $prompt,
//            'negative_prompt' => '(lowres, low quality, worst quality:1.2), (text:1.2), watermark, glitch, deformed, mutated, cross-eyed, ugly, disfigured (lowres, low quality, worst quality:1.2), (text:1.2), watermark, painting, drawing, illustration, glitch,deformed, mutated, cross-eyed, ugly, disfigured',
            "negative_prompt" => "(lowres, low quality, worst quality:1.2), (text:1.2), watermark, painting, drawing, illustration, glitch, deformed, mutated, cross-eyed, ugly, disfigured (lowres, low quality, worst quality:1.2), (text:1.2), watermark, glitch,deformed, mutated, cross-eyed, ugly, disfigured",
            'sdxl_weights'    => 'stable-diffusion-xl-base-1.0',
//            'sdxl_weights'    => 'protovision-xl-high-fidel',
            'width'           => 640,
            'height'          => 640,
            'scheduler' => 'EulerDiscreteScheduler',
            'num_inference_steps' => 30,

            'guidance_scale' => 16.63,
//            'guidance_scale'  => 15,
            'ip_adapter_scale'  => 0.8,
            'controlnet_conditioning_scale'  => 0.8,
            'enable_pose_controlnet' => true,
            'pose_strength' => 0.03,
//            "pose_strength" => 0.17,
//            'scheduler' => 'DPMSolverMultistep',
            'seed'            => $image->book->additional_data["imageSeed"] ?? null,

            'enable_lcm' => false,
            'canny_strength' => 0.3,
            'depth_strength' => 0.5,
            'lcm_guidance_scale' => 1.5,
            'enhance_nonface_region' => true,
            'enable_canny_controlnet' => false,
            'enable_depth_controlnet' => false,
            'lcm_num_inference_steps' => 5,
        ];

        Log::debug("[ReplicateInstantId][create] Requesting ", $input + ["version" => $version]);

        $response = $this->client->predictions()->list();
        $response = $this->client->predictions()
            ->withWebhook(config("app.url") . "/api/replicate/instant-id/{$image->book->uuid}/{$image->id}")
            ->create($version, $input);

        Log::debug("[ReplicateInstantId][create] Got response for prompt {$prompt}", (array) $response);

        return new GenerateImageResult($response->id, GenerateImageStatuses::Initial, []);
    }
}
