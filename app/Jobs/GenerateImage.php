<?php

namespace App\Jobs;

use App\Actions\GenerateBook;
use App\Models\Book;
use App\Models\Image;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Images\CreateResponse;

class GenerateImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Image $image)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        \Log::debug("[GenerateImage][handle] Got image {$this->image->id} with prompt '{$this->image->prompt}'");

        $map =[
            'Illustrate the children discovering the stream and planning their camp.' => '{"created":1707177247,"data":[{"url":"https://gcdnb.pbrd.co/images/CFYZEKxu6jW7.png","revised_prompt":"An image depicting an adventurous scene. A group of children with varied descents including Caucasian, Black, Middle-Eastern, and South Asian genders are exploring a lush forest. They stumble upon a crystal clear stream, shimmering under the sunlight that pierces through the dense canopy of leaves. The children, with expressions of curiosity and excitement on their faces, begin to gather sticks and leaves, planning for their camp. They\'re envisaging a small, friendly campsite beside the stream, with a horde of their self-made tents whimsically placed, and a crackling yellow fire merrily dancing under the sky."}]}',
            'Illustrate the children working together, using their creativity to build their camp.' => '{"created":1707178282,"data":[{"url":"https://gcdnb.pbrd.co/images/0LTMtSaeDf4z.png","revised_prompt":"A detailed scene portraying several children of diverse descents working together harmoniously. In the composition’s center, an African-American girl, an Asian boy, a Middle-Eastern boy, and a Caucasian girl utilize their creativity and teamwork to construct a makeshift camp. Amidst a luscious green forest, they gather natural resources like branches, leaves, and stones. Their faces beam with satisfaction and determination, reflecting the joy of collaboration. Light filters down through the forest canopy, highlighting the children and their ongoing project, providing a symbol of their shared creativity and admiration for nature."}]}',
            'Illustrate the children exploring nature and observing the stream.' => '{"created":1707178294,"data":[{"url":"https://gcdnb.pbrd.co/images/VjJdtx2pdqyY.png","revised_prompt":"A harmonious image of a diverse group of children exploring the nature around them. These children vary in gender and descent, including a Caucasian boy, a Black girl, a Hispanic boy, and a South Asian girl. They are huddled around a stream, filled with curiosity and fascination. Their eyes sparkle with excitement as they peer into the clear, flowing water, catching glimpses of tiny fish darting around. The surrounding area is vibrant with lush green grass, towering trees, and blossoming flowers. The children are knee-deep, feeling the cool current as it gently tugs at their little legs."}]}',
            'Illustrate the children embarking on their adventure, hand in hand, with smiles on their faces.' => '{"created":1707178306,"data":[{"url":"https://gcdnb.pbrd.co/images/laIL3V5FuL1i.png","revised_prompt":"A scene featuring six jovial children embarking on a grand adventure. They\'re all holding hands, forming a unified group. There\'s a blend of cultures with two Caucasian kids, an Hispanic child, a Black boy, a Middle-Eastern girl, and a South Asian kid. They all wear casual hiking clothes suitable for an outdoor adventure. Their faces beam with excitement and joy, showcasing brightly colored bandanas adorning their heads and backpacks filled with supplies on their shoulders, prepared for the journey ahead."}]}',
            'Illustrate the children sitting together, reminiscing and cherishing the memories they made at the camp.' => '{"created":1707178315,"data":[{"url":"https://gcdnb.pbrd.co/images/niEZuKYNtX2n.png","revised_prompt":"An image of a group of children of various descents, including Caucasian, Hispanic, Black, Middle-Eastern, and South Asian sitting together. They are in a clearing, surrounded by the remnants of a camp: a dwindling campfire, a few scattered tents and sleeping bags. The sunset paints the sky with hues of orange and pink, casting long shadows. Despite the dwindling light, their faces are lit with joy and nostalgia as they reminisce and cherish the memories they made at the camp."}]}',
        ];
        if ($fake = $map[$this->image->prompt] ?? null) {
            usleep(500000);

            $response = json_decode($fake);
            \Log::debug("[GenerateImage][handle] Got image {$this->image->id} data", json_decode($fake, true));
        } else {

            /** @var CreateResponse $response */
            $response = retry(1, fn() => OpenAI::images()->create([
                'model'           => 'dall-e-3',
                'prompt'          => \Str::remove("Illustrate ", $this->image->prompt),
                'n'               => 1,
                'size'            => '1024x1024',
                'response_format' => 'url',
            ]));

            \Log::debug("[GenerateImage][handle] Got image {$this->image->id} data", $response->toArray());
        }
        if (count($response->data) != 1) {
            \Log::critical("[GenerateImage][handle] Why no 1?!");
        }

        $data = $response->data[0];

        $this->image->book->newQuery()->incrementEach(["additional_data->costs_usd" => "0.040"]);
        $this->image->image_url = $data->url;
        $this->image->save();

        dispatch(new DownloadImage($this->image));
    }
}
