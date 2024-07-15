<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 08/04/2024
 * Time: 1:10
 *
 * https://replicate.com/tencentarc/photomaker-style?prediction=nr9mx0cyh9rgm0cfz77sxp5sp8
 */

namespace App\AI\Art;

use App\AI\GenerateAIStatuses;
use App\Models\Character;
use App\Models\Image;
use BenBjurstrom\Replicate\Replicate;
use Log;
use Str;

class ReplicatePhotomakerStyle
{

    public function __construct(private Replicate $client)
    {
    }

    public function create(Image $image): GenerateImageResult
    {
        $prompt = Str::of($image->prompt);
        if ($prompt->contains("image") && !$prompt->contains("img")) {
            $image->prompt = $prompt->replaceFirst("image", "img")->remove("Illustrate ");
            Log::debug("[ReplicatePhotomakerStyle][create] fixing from '{$prompt}' to '{$image->prompt}'");
            $image->save();
        }
        if ($prompt->substrCount("image") > 1) {
            $placeholder = Str::uuid()->toString();
            $image->prompt = $prompt->replaceFirst("img", $placeholder)->remove("img")->replace($placeholder, "img");
            Log::debug("[ReplicatePhotomakerStyle][create] fixing from '{$prompt}' to '{$image->prompt}'");
            $image->save();
        }

        $version = '467d062309da518648ba89d226490e02b8ed09b5abc15026e54e31c5a8cd0769';

        $imgPath = null;
        if ($characterId = $image->book->additional_data["request"]["character"] ?? null) {
            $imgPath = url(Character::find($characterId)->image_path);
        }

        $input   = [
//            'model'           => 'stable-diffusion-2-1',
            'image'           => $imgPath,
            'prompt'          => $image->prompt,
            "negative_prompt" => "realistic, photo-realistic, worst quality, greyscale, bad anatomy, bad hands, error, text",

//            "prompt" => "A girl img riding dragon over a whimsical castle, 3d CGI, art by Pixar, half-body, screenshot from animation",
            "num_steps" => 50,
            "style_name" => "(No style)",
            "input_image" => $imgPath,
            "num_outputs" => 1,
            "guidance_scale" => 5,
            "style_strength_ratio" => 20,

            'seed'            => $image->book->additional_data["imageSeed"] ?? null,
        ];

        Log::debug("[ReplicatePhotomakerStyle][create] Requesting ", $input + ["version" => $version]);

        $response = $this->client->predictions()
            ->withWebhook(config("app.url") . "/api/replicate/photomaker-style/{$image->book->uuid}/{$image->id}")
            ->create($version, $input);

        Log::debug("[ReplicatePhotomakerStyle][create] Got response for prompt {$prompt}", (array) $response);

        return new GenerateImageResult($response->id, GenerateAIStatuses::Initial, []);
    }
}
